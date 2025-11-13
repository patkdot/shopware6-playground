<?php

declare(strict_types=1);

namespace KdotPlayground\Flow\Event;

use KdotPlayground\Flow\Aware\KdotAware;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Symfony\Contracts\EventDispatcher\Event;

class KdotEvent extends Event implements KdotAware, FlowEventAware
{
    public const EVENT_NAME = 'kdot.event';

    public function __construct(private array $productUpsertData, private Context $context) {}

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getKdotData(): array
    {
        return $this->productUpsertData;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return new EventDataCollection();
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
