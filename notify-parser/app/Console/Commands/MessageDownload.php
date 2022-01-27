<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dto\OptionsSearchMessagesDto;
use App\Dto\OptionsSearchRegExpMessagesDto;
use App\Models\Publisher;
use App\Models\Repositories\MessageRepository;
use App\Models\Repositories\PublisherRepository;
use App\Services\Search\SearchFactoryFactory;
use App\Services\Search\Types\FindInterface;
use App\Services\Search\Types\RegExp;
use App\Services\SubscriberService;
use App\Services\Telegram\TelegramApi\HistoryMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;
use Throwable;
use function Amp\File\exists;

class MessageDownload extends Command
{
    protected $signature = 'message:download';

    protected $description = 'Download messages of a group or channel';

    private FindInterface $searchType;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        try {
            $tasks = (new PublisherRepository())->getActivePublishers();
            foreach ($tasks as $taskData) {
                $this->searchType = (new SearchFactoryFactory($taskData->search_type))->get();
                $messageLastId = (new MessageRepository())->getLastMessage($taskData->publisher_id);

                if ($messageLastId === null) {
                    $messages = (new HistoryMessage($taskData->publisher_name))->get();
                    if (count($messages['messages']) > 0) {
                        $this->saveMessages($taskData, $messages);
                        $this->sendNotification($taskData, $messages);
                        $messageLastId = $messages['messages'][0]['id'];
                    }
                }

                if ($messageLastId !== null) {
                    do {
                        $messages = (new HistoryMessage($taskData->publisher_name))
                            ->setMinId($messageLastId)
                            ->get();

                        if (count($messages['messages']) > 0) {
                            $messageLastId = $messages['messages'][array_key_last($messages['messages'])]['id'];
                            $this->saveMessages($taskData, $messages);
                            $this->sendNotification($taskData, $messages);
                        }
                    } while (count($messages) === 100);
                }
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            Log::channel('message_download')->error($e->getMessage());
        }
    }

    private function saveMessages(Publisher $publisher, array $messages): void
    {
        try {
            if ($this->searchType instanceof RegExp === false) {
                $insertMessage = SubscriberService::saveMessagesGroup($publisher, $messages);
                $this->info('Successful saved: ' . $insertMessage);
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            Log::channel('message_download')->error($e->getMessage());
        }
    }

    private function sendNotification(Publisher $publisher, array $messages): void
    {
        try {
            if ($this->searchType instanceof RegExp) {
                $dtoOption = $this->generateDtoOptionRegExpMessages(
                    $publisher->search_text,
                    $messages
                );
            } else {
                $dtoOption = $this->generateDtoOptionMessages(
                    $publisher->search_text,
                    $publisher->publisher_id,
                    $messages['messages']
                );
            }

            $notifications = $this->searchType->find($dtoOption);

            if (count($notifications) > 0) {
                (new SubscriberService())->sendSubscribers($publisher, $notifications);
            }
        } catch (Throwable $e) {
            $this->comment($e->getMessage());
        }
    }

    #[Pure]
    private function generateDtoOptionMessages(
        string $searchText,
        int    $publisherId,
        array  $messages
    ): OptionsSearchMessagesDto
    {
        return new OptionsSearchMessagesDto(
            $searchText,
            $publisherId,
            $messages[array_key_last($messages)]['id'],
            $messages[0]['id'],
        );
    }

    #[Pure]
    private function generateDtoOptionRegExpMessages(
        string $searchText,
        array  $messages
    ): OptionsSearchRegExpMessagesDto
    {
        return new OptionsSearchRegExpMessagesDto(
            $searchText,
            $messages,
        );
    }
}
