<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use App\Models\Publisher;
use App\Models\Repositories\PublisherRepository;
use App\Models\Repositories\SubscriberRepository;
use App\Models\Sender;
use App\Models\Subscriber;
use App\Models\Task;
use App\Services\Telegram\TelegramApi\HistoryMessage;
use App\Services\Telegram\TelegramBotApi;
use DateTime;
use Illuminate\Support\Facades\DB;

class SubscriberService
{
    private SubscriberRepository $subscriberRepository;

    public function __construct()
    {
        $this->subscriberRepository = new SubscriberRepository();
    }

    public static function store(Task $task, array $subscribers): Task
    {
        /** @var Subscriber $subscriber */
        foreach ($subscribers as $subscriber) {
            $subscriberEntity = (new Subscriber())
                ->newQuery()
                ->where(['name' => $subscriber['name']])
                ->first();

            $senderEntity = (new Sender())
                ->newQuery()
                ->where(['name' => $subscriber['sender']['name']])
                ->first();

            if (is_null($subscriberEntity)) {
                $subscriberEntity = new Subscriber();
                $subscriberEntity->name = $subscriber['name'];
            }

            $task->subscribers()->save($subscriberEntity);

            if ($subscriberEntity !== null && $senderEntity !== null) {
                $subscriberSender = DB::table('subscriber_sender')->where(
                    [
                        'subscriber_id' => $subscriberEntity->id,
                        'sender_id' => $senderEntity->id,
                    ]
                )->first();

                if ($subscriberSender !== null) {
                    continue;
                }
            }

            if (is_null($senderEntity)) {
                $senderEntity = new Sender();
                $senderEntity->name = $subscriber['sender']['name'];
                $senderEntity->type = $subscriber['sender']['type'];
            }

            $subscriberEntity->senders()->save($senderEntity);
        }

        return $task;
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendSubscribers(Publisher $taskData, array $notifications): void
    {
        $subscribers = $this->subscriberRepository->getActiveSubscriber($taskData->task_id);

        foreach ($subscribers as $subscriber) {
            foreach ($notifications as $notification) {
                $date = date('Y-m-d H:i:s', $notification['public_date']);

                $message =
                    <<<END
*Название события:* `{$taskData->task_name}`
*Ссылка на сообщение:* [перейти](https://t.me/{$taskData->publisher_name}/{$notification['public_id']})
*Поисковой запрос:* `{$taskData['search_text']}`
*Автор:* `{$notification['user_name']} {$notification['user_last_name']}`
*Сообщение:* `{$notification['message']}`
*Дата:* `{$date}`
END;

                (new TelegramBotApi($subscriber->sender_name))
                    ->sendMessage(
                        $subscriber->subscriber_name,
                        $message
                    );
            }
        }
    }

    public static function saveMessagesGroupBatch(string $name): ?int
    {
        $minId = 0;
        $offsetId = 100;
        $messagesRev = [];

        do {
            $messages = (new HistoryMessage($name))
                ->setOffsetId($offsetId)
                ->setMinId($minId)
                ->get();

            if (empty($messages['messages'])) {
                break;
            }

            $messagesRev = array_reverse($messages['messages']);

            foreach ($messagesRev as $item) {
                if (!empty($item['message'])) {
                    if (!empty($item['from_id']['user_id'])) {
                        $user = SubscriberService::findUser($item['from_id']['user_id'], $messages['users']);
                    } else {
                        $user = [];
                    }

                    $messageEntity = Message::where([
                        'public_id' => $item['id'],
                        'channel_id' => $item['peer_id']['channel_id'],
                    ])->first();

                    if (is_null($messageEntity)){
                        $message = new Message();
                        $message->channel_id = $item['peer_id']['channel_id'];
                        $message->user_id = !empty($item['from_id']['user_id']) ? $item['from_id']['user_id'] : null;
                        $message->user_name = !empty($user['first_name']) ? $user['first_name'] : '';
                        $message->user_last_name = !empty($user['last_name']) ? $user['last_name'] : '';
                        $message->message = $item['message'];
                        $message->public_id = $item['id'];
                        $message->public_date = $item['date'];
                        $message->created_at = (new DateTime())->format('Y-m-d H:i:s');
                        $message->save();
                    }
                }
            }

            $minId += 100;
            $offsetId += 100;
        } while (!empty($messages['messages']));

        return $messagesRev[0]['id'] ?? null;
    }

    public static function saveMessagesGroup(Publisher $task, array $messages): ?int
    {
        $insertMessage = 0;
        $messagesRev = array_reverse($messages['messages']);
        foreach ($messagesRev as $item) {
            if (!empty($item['message'])) {
                /** @var Publisher $publisher */
                $publisher = Publisher::find($task->publisher_id);

                if (!empty($item['from_id']['user_id'])) {
                    $user = static::findUser($item['from_id']['user_id'], $messages['users']);
                } else {
                    $user = [];
                }

                $messageEntity = Message::where([
                    'public_id' => $item['id'],
                    'channel_id' => $item['peer_id']['channel_id'],
                ])->first();

                if (is_null($messageEntity)) {
                    $message = new Message();
                    $message->channel_id = $item['peer_id']['channel_id'];
                    $message->user_id = !empty($item['from_id']['user_id']) ? $item['from_id']['user_id'] : null;
                    $message->user_name = !empty($user['first_name']) ? $user['first_name'] : '';
                    $message->user_last_name = !empty($user['last_name']) ? $user['last_name'] : '';
                    $message->message = $item['message'];
                    $message->public_id = $item['id'];
                    $message->public_date = $item['date'];
                    $message->created_at = (new DateTime())->format('Y-m-d H:i:s');

                    $publisher->messages()->save($message);
                    $insertMessage++;
                }
            }
        }

        return $insertMessage;
    }

    public static function findUser(int $userId, array $users): array
    {
        $res = [];
        $userIds = array_column($users, 'id');
        $keyUser = array_search($userId, $userIds);

        if ($keyUser !== false) {
            $res = $users[$keyUser];
        }

        return $res;
    }
}
