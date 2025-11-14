<?php

declare(strict_types=1);

namespace KdotPlayground\Subscriber;

use KdotPlayground\Flow\Event\KdotEvent;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BusinessEventCollectorSubscriber
{
    private BusinessEventCollector $businessEventCollector;

    public function __construct(BusinessEventCollector $businessEventCollector)
    {
        $this->businessEventCollector = $businessEventCollector;
    }

    #[AsEventListener(event: BusinessEventCollectorEvent::NAME)]
    public function onBusinessEventCollectorEvent(BusinessEventCollectorEvent $event): void
    {
        $collection = $event->getCollection();

        $definition = $this->businessEventCollector->define(KdotEvent::class);

        if (!$definition) {
            return;
        }

        $collection->set($definition->getName(), $definition);
    }
}
