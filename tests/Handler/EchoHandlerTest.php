<?php
declare(strict_types=1);

namespace Tomaj\Hermes\Test\Handler;

use PHPUnit_Framework_TestCase;
use Tomaj\Hermes\Message;
use Tomaj\Hermes\Handler\EchoHandler;

class EchoHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testEchoHandler()
    {
        $message = new Message('message1key', ['a' => 'b']);
        $output = "Received message: #{$message->getId()} (type message1key)\n";
        $output .= "Payload: {\"a\":\"b\"}\n";
        $this->expectOutputString($output);
        
        $echoHandler = new EchoHandler();
        $echoHandler->handle($message);
    }
}
