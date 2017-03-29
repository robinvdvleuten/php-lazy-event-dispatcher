<?php

namespace Rvdv\LazyEventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LazyEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $queue;

    /**
     * @var bool
     */
    private $lazy;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->queue = [];
        $this->lazy = true;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        if ($this->lazy) {
            $this->queue[] = [$eventName, $event];

            return $event;
        }

        return $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * Flush the queue so any queued event will be dispatched.
     */
    public function flush()
    {
        if ($this->lazy) {
            $this->lazy = false;

            while ($args = array_shift($this->queue)) {
                $this->dispatch(...$args);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Adds a service as event listener.
     *
     * @param string $eventName
     * @param array  $callback
     * @param int    $priority
     */
    public function addListenerService($eventName, $callback, $priority = 0)
    {
        $this->dispatcher->addListenerService($eventName, $callback, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * Adds a service as event subscriber.
     *
     * @param string $serviceId
     * @param string $class
     */
    public function addSubscriberService($serviceId, $class)
    {
        $this->dispatcher->addSubscriberService($serviceId, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        $this->dispatcher->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->removeSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }
}
