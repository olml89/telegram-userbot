<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Bot\MadelineProto;

use danog\MadelineProto\EventHandler;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\Plugin\RestartPlugin;
use danog\MadelineProto\SimpleEventHandler;

/**
 * Simple handler to vinculate the MTProto API lifecycle to our message processing logic.
 */
final class BotEventHandler extends SimpleEventHandler
{
    /**
     * Get peer(s) where to report errors.
     *
     * @return string[]
     */
    public function getReportPeers(): array
    {
        return [
            //
        ];
    }

    /**
     * Returns a set of plugins to activate.
     * See here for more info on plugins: https://docs.madelineproto.xyz/docs/PLUGINS.html
     *
     * @return class-string<EventHandler>[]
     */
    public static function getPlugins(): array
    {

        return [
            RestartPlugin::class,
        ];
    }

    /**
     * Handle incoming updates from users, chats and channels.
     */
    #[Handler]
    public function handleMessage(PrivateMessage $message): void
    {
        if ($message->silent) {
            return;
        }

        $message->sendText(mb_strtoupper($message->message), silent: true);
    }
}
