<?php

namespace Tomaj\Hermes\Test;

use PHPUnit_Framework_TestCase;
use Tomaj\Hermes\Test\Driver\DummyDriver;
use Tomaj\Hermes\Dispatcher;
use Tomaj\Hermes\Message;

class EmitTest extends PHPUnit_Framework_TestCase
{
    public function testEmitWithDummyDriver()
    {
        $driver = new DummyDriver();
        $dispatcher = new Dispatcher($driver);

        $this->assertNull($driver->getMessage());

        $dispatcher->emit(new Message('event-type', ['content']));

        $message = $driver->getMessage();
        $this->assertEquals('event-type', $message->getType());
        $this->assertEquals(['content'], $message->getPayload());
    }
}
