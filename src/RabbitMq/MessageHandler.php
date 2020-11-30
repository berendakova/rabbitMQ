<?php


namespace App\RabbitMq;


use App\Console\RabbitCommand;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageHandler
{
    protected $connection;
    public const PICTURE_QUEUE = 'picture_queue';
    /**
     * MessageHandler constructor.
     * @param AMQPStreamConnection $connection
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            RabbitCommand::HOST, RabbitCommand::PORT,  RabbitCommand::USERNAME,  RabbitCommand::PASSWORD);
    }

    public function addMessage(string $pictureId) {
        $channel = $this->connection->channel();
        $channel->queue_declare(
            self::PICTURE_QUEUE,
            false,
            false,
            false,
            false);

        $message = new AMQPMessage($pictureId);
        $channel->basic_publish(
            $message,
            '',
            self::PICTURE_QUEUE
        );
        $channel->close();
        try {
            $this->connection->close();
        } catch (\Exception $e) {
        }
    }

}