<?php

declare(strict_types=1);

namespace KdotPlayground\Message;

use Shopware\Core\Framework\Context;

class KdotUpsertMessage
{
    /**
     * @param list<array<string, array<string, array<string, string>>|string|true>> $upserts
     */
    public function __construct(
        private array $upserts,
        private Context $context,
        private bool $isSync = true,
    ) {
    }

    /**
     * @return list<array<string, array<string, array<string, string>>|string|true>>
     */
    public function getUpserts(): array
    {
        return $this->upserts;
    }

    /**
     * @param list<array<string, array<string, array<string, string>>|string|true>> $upserts
     */
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
