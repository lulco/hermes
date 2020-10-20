<?php
declare(strict_types=1);

namespace Tomaj\Hermes\Test;

use PHPUnit\Framework\TestCase;
use Tomaj\Hermes\Test\Driver\DummyDriver;
use Tomaj\Hermes\Emitter;
use Tomaj\Hermes\Message;

/**
 * Class EmitTest
 * @package Tomaj\Hermes\Test
 * @covers Emitter
 */
class EmitTest extends TestCase
{
    public function testEmitWithDummyDriver()
    {
        $driver = new DummyDriver();
        $emitter = new Emitter($driver);

        $this->assertNull($driver->getMessage());

        $emitter->emit(new Message('event-type', ['content']));

        $message = $driver->getMessage();
        $this->assertEquals('event-type', $message->getType());
        $this->assertEquals(['content'], $message->getPayload());
    }
}
