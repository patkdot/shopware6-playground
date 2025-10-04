<?php

declare(strict_types=1);

namespace KdotPlayground\Message;

use Shopware\Core\Framework\Context;

class KdotUpsertMessage
{
    public function __construct(
        private array $upserts,
        private Context $context,
        private bool $isSync = true,
    ) {
    }

    public function getUpserts(): array
    {
        return $this->upserts;
    }

    public function addUpserts(array $upserts): void
    {
        $this->upserts = array_merge($this->upserts, $upserts);
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function isSync(): bool
    {
        return $this->isSync;
    }
}