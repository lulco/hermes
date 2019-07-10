<?php
declare(strict_types=1);

namespace Tomaj\Hermes\Driver;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Tomaj\Hermes\MessageInterface;
use Tomaj\Hermes\MessageSerializer;

class LazyRabbitMqDriver implements DriverInterface
{
    use SerializerAwareTrait;

    /** @var AMQPLazyConnection */
    private $connection;
    
    /** @var AMQPChannel */
    private $channel;

    /** @var string */
    private $queue;
    
    /**
     * @param AMQPLazyConnection $connection
     * @param string $queue
     */
    public function __construct(AMQPLazyConnection $connection, string $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->serializer = new MessageSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message): bool
    {
        $rabbitMessage = new AMQPMessage($this->serializer->serialize($message), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->getChannel()->basic_publish($rabbitMessage, '', $this->queue);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function wait(Closure $callback): void
    {
        $this->getChannel()->basic_consume(
            $this->queue,
            '',
            false,
            false,
            false,
            false,
            function ($rabbitMessage) use ($callback) {
                $message = $this->serializer->unserialize($rabbitMessage->body);
                $callback($message);
                $rabbitMessage->delivery_info['channel']->basic_ack($rabbitMessage->delivery_info['delivery_tag']);
            }
        );

        while (count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
    }
    
    private function getChannel(): AMQPChannel
    {
        if ($this->channel) {
            return $this->channel;
        }
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->basic_qos(null, 1, null);
        return $this->channel;
    }
}
