<?php declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class KdotTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KdotTranslationEntity::class;
    }
}