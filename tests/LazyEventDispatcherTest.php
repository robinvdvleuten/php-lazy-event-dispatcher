<?php

namespace Rvdv\LazyEventDispatcher\Tests;

use PHPUnit\Framework\MockObject\MockObject as Mock;
use PHPUnit\Framework\TestCase;
use Rvdv\LazyEventDispatcher\LazyEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LazyEventDispatcherTest extends TestCase
{
    /**
     * @var Mock|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LazyEventDispatcher
     */
    private $lazyDispatcher;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->lazyDispatcher = new LazyEventDispatcher($this->eventDispatcher);
    }

    public function testDispatcherEnqueuesDispatchedEvents()
    {
        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        $this->lazyDispatcher->dispatch('foo');
    }

    public function testDispatcherReturnsGivenEventInstance()
    {
        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        $event = $this->createMock(Event::class);

        $this->assertSame($event, $this->lazyDispatcher->dispatch('foo', $event));
    }

    public function testDispatcherClearsQueuedEventsWhenFlushed()
    {
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(['foo'], ['bar']);

        $this->lazyDispatcher->dispatch('foo');
        $this->lazyDispatcher->dispatch('bar');

        $this->lazyDispatcher->flush();
    }

    public function testDispatcherPassesEventsToEventDispatcherOnlyOnceWhenFlushed()
    {
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with('foo');

        $this->lazyDispatcher->dispatch('foo');

        $this->lazyDispatcher->flush();
        $this->lazyDispatcher->flush();
    }

    public function testDispatcherBecomesLazyAfterFlushing()
    {
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with('foo');

        $this->lazyDispatcher->dispatch('foo');

        $this->lazyDispatcher->flush();

        $this->lazyDispatcher->dispatch('bar');
    }
}
