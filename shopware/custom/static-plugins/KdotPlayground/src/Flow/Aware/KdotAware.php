<?php

declare(strict_types=1);

namespace KdotPlayground\Flow\Aware;

use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\IsFlowEventAware;

#[IsFlowEventAware]
interface KdotAware extends FlowEventAware
{
    public const KDOT_DATA = 'kdotData';

    /**
     * @return array<string, mixed>
     */
    public function getKdotData(): array;
}
