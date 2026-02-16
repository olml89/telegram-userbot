<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\BotManager\Websocket;

use Exception;
use JsonException;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandFactory;
use olml89\TelegramUserbot\BotManager\Bot\Command\CommandRunner;
use olml89\TelegramUserbot\BotManager\Bot\Command\DisallowedCommandTypeException;
use olml89\TelegramUserbot\BotManager\Bot\Command\InvalidCommandTypeException;
use olml89\TelegramUserbot\BotManager\Bot\Command\LogRecord\IncomingCommand;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusInitializer;
use olml89\TelegramUserbot\BotManager\Bot\Status\StatusManager;
use olml89\TelegramUserbot\BotManager\Websocket\LogRecord\ClosedConnection;
use olml89\TelegramUserbot\BotManager\Websocket\LogRecord\Listening;
use olml89\TelegramUserbot\BotManager\Websocket\LogRecord\OpenedConnection;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\InvalidPhoneCodeException;
use olml89\TelegramUserbot\Shared\Error\SentryReporter;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\ErrorLogRecord;
use olml89\TelegramUserbot\Shared\Logger\LogRecord\LoggableLogger;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Throwable;

/**
 * Implements a Ratchet websocket server
 */
final readonly class WebSocketServer implements MessageComponentInterface
{
    public function __construct(
        private WebSocketConnectionPool $socketConnectionPool,
        private StatusManager $statusManager,
        private CommandFactory $commandFactory,
        private CommandRunner $commandRunner,
        private LoggableLogger $loggableLogger,
        private SentryReporter $sentryReporter,
    ) {}

    public function listen(StatusInitializer $statusInitializer, WebSocketServerConfig $config): void
    {
        /**
         * Initialize Status of the StatusManager
         */
        $statusInitializer->initialize();

        /**
         * Listen to WebSocket connections
         */
        $loop = Loop::get();

        new IoServer(
            app: new HttpServer(
                component: new WsServer($this),
            ),
            socket: new SocketServer(
                uri: $config->uri(),
                loop: $loop,
            ),
            loop: $loop,
        );

        $this->loggableLogger->log(new Listening($config));
        $loop->run();
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        // Add new connection to the pool
        $webSocketConnection = $this->socketConnectionPool->add($conn);
        $this->loggableLogger->log(new OpenedConnection($webSocketConnection));

        // Emit current status to the connections
        $this->statusManager->emit();
    }

    public function onClose(ConnectionInterface $conn): void
    {
        // Remove connection from the pool
        $webSocketConnection = $this->socketConnectionPool->remove($conn);
        $this->loggableLogger->log(new ClosedConnection($webSocketConnection));
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        // Log the exception
        $this->loggableLogger->log(new ErrorLogRecord('Error on the websocket server loop', $e));

        // Report to Sentry
        $this->sentryReporter->report($e);

        // Emit the current status with the exception message (and stack trace on development)
        $this->statusManager->emit($e);
    }

    /**
     * @param string $msg
     *
     * @throws JsonException
     * @throws InvalidCommandTypeException
     * @throws InvalidPhoneCodeException
     * @throws DisallowedCommandTypeException
     * @throws Throwable
     */
    public function onMessage(ConnectionInterface $from, $msg): void
    {
        // Create a command from an incoming message
        $command = $this->commandFactory->fromJson($msg);
        $this->loggableLogger->log(new IncomingCommand($command));

        // Run the command (catching the exceptions to log them on the command channel, then they are rethrown
        $this->commandRunner->run($command);
    }
}
