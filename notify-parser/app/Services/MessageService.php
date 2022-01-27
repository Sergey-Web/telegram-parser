<?php

namespace App\Services;

use App\Dto\OptionsSearchMessagesDto;
use App\Dto\OptionsSearchRegExpMessagesDto;
use App\Models\Repositories\MessageRepository;
use DateTime;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    public function findLikeMessages(OptionsSearchMessagesDto $optionsSearchMessagesDto): array
    {
        $searchWords = static::getListSearchWords($optionsSearchMessagesDto->getTextSearch());

        return (new MessageRepository())->searchLikeMessage(
            $this->getSearchQueryLike($searchWords),
            $optionsSearchMessagesDto->getPublisherId(),
            $optionsSearchMessagesDto->getStartMessageId(),
            $optionsSearchMessagesDto->getEndMessageId(),
        );
    }

    public function findFullTextMessages(OptionsSearchMessagesDto $optionsSearchMessagesDto): array
    {
        $searchWords = static::getListSearchWords($optionsSearchMessagesDto->getTextSearch());

        return (new MessageRepository())->searchFullText(
            $this->getSearchQueryFullText($searchWords),
            $optionsSearchMessagesDto->getPublisherId(),
            $optionsSearchMessagesDto->getStartMessageId(),
            $optionsSearchMessagesDto->getEndMessageId(),
        );
    }

    public function findRegExpMessages(OptionsSearchRegExpMessagesDto $optionsSearchMessagesDto): array
    {
        $searchWords = static::getSearchQueryRexExp(
            static::getListSearchWords($optionsSearchMessagesDto->getTextSearch())
        );

        $lastMessageId = 0;
        $channelId = $optionsSearchMessagesDto->getMessages()['messages'][0]['peer_id']['channel_id'];
        if (Storage::disk('regexp')->exists($channelId . '.csv')) {
            $lastMessageId = (int) Storage::disk('regexp')->get('last_message.csv');
        }

        $messages = [];
        $messagesRawDataRev = array_reverse($optionsSearchMessagesDto->getMessages()['messages']);
        foreach ($messagesRawDataRev as $message) {
            if ($lastMessageId < $message['id'] && isset($message['message'])) {
                preg_match('/' . $searchWords . '/i', $message['message'], $matches);
                if (!empty($matches)) {
                    if (!empty($message['from_id']['user_id'])) {
                        $user = SubscriberService::findUser(
                            $message['from_id']['user_id'],
                            $optionsSearchMessagesDto->getMessages()['users']
                        );
                    } else {
                        $user = [];
                    }
                    $messages[] = [
                        'channel_id' => $message['peer_id']['channel_id'],
                        'user_id' => !empty($message['from_id']['user_id']) ? $message['from_id']['user_id'] : null,
                        'user_name' => !empty($user['first_name']) ? $user['first_name'] : '',
                        'user_last_name' => !empty($user['last_name']) ? $user['last_name'] : '',
                        'message' => $message['message'],
                        'public_id' => $message['id'],
                        'public_date' => $message['date'],
                        'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        Storage::disk('regexp')
            ->put(
                $channelId . '.csv',
                $optionsSearchMessagesDto->getMessages()['messages'][0]['id']
            );

        return $messages;
    }

    private function getSearchQueryLike(array $searchWords): string
    {
        $searchText = '';
        foreach($searchWords as $item) {
            $searchText .= " message ILIKE '%" . trim($item) . "%' OR";
        }

        return mb_substr($searchText, 0, -3);
    }

    private function getSearchQueryFullText(array $searchWords): string
    {
        $searchFullText = [];

        foreach ($searchWords as $item) {
            $searchFullText[] = str_replace([' '], ' <-> ', trim($item));
        }

        return implode(' | ', $searchFullText);
    }

    private function getSearchQueryRexExp(array $searchWords): string
    {
        $searchText = '';
        foreach($searchWords as $item) {
            $searchText .= '(' . $item . ')|';
        }

        return mb_substr($searchText, 0, -1);
    }

    public static function getListSearchWords(string $searchText): array
    {
        return explode(' | ', $searchText);
    }
}
