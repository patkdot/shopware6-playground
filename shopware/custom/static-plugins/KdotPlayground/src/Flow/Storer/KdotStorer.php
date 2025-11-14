<?php

declare(strict_types=1);

namespace KdotPlayground\Flow\Storer;

use KdotPlayground\Flow\Aware\KdotAware;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Flow\Dispatching\Storer\FlowStorer;
use Shopware\Core\Framework\Event\FlowEventAware;

class KdotStorer extends FlowStorer
{
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof KdotAware || isset($stored['kdot'])) {
            return $stored;
        }

        $stored['kdot'] = $event->getKdotData();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore('kdot')) {
            return;
        }

        $storable->setData('kdot', $storable->getStore('kdot'));
    }
}
