<?php

declare(strict_types=1);

namespace App\Services\Telegram\TelegramApi;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use DirectoryIterator;
use Throwable;

class TelegramApi implements TelegramApiInterface
{
    private API $api;

    private const PATH_SESSION = '/../../../../storage/framework/session_telegram/';
    private const PATH_PUBLIC = '/../../../../public/';

    public function __construct()
    {
        $api = new API(__DIR__ . static::PATH_SESSION . 'session.madeline');
        $api->async(false);
        $api->start();

        $this->api = $api;
    }

    public function api(): API
    {
        return $this->api;
    }

    public function deleteSessionFile(): bool
    {
        $res = true;
        try {
            $sessionFiles = (new DirectoryIterator(__DIR__ . static::PATH_SESSION));

            /** @var DirectoryIterator $file */
            foreach ($sessionFiles as $file) {
                if (str_contains($file->getFilename(), 'session.')) {
                    unlink(__DIR__ . static::PATH_SESSION . $file->getFilename());
                }
            }
        } catch (Throwable $e) {
            Logger::log($e->getMessage(), E_ERROR);
            $res = false;
        }

        return $res;
    }

    public function deletePublicFile(): bool
    {
        $publicFiles = (new DirectoryIterator(__DIR__ . static::PATH_PUBLIC));

        $res = true;
        try {
            /** @var DirectoryIterator $file */
            foreach ($publicFiles as $file) {
                if (str_contains(mb_strtolower($file->getFilename()), 'madeline')) {
                    unlink(__DIR__ . static::PATH_PUBLIC . $file->getFilename());
                }
            }
        } catch (Throwable $e) {
            Logger::log($e->getMessage(), E_ERROR);
            $res = false;
        }

        return $res;
    }

    public function deleteLogFile(): bool
    {
        $res = false;
        $file = __DIR__ . '/../../../MadelineProto.log';
        if (file_exists($file) === true) {
            $res = unlink($file);
        }

        return $res;
    }
}

