<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<KdotEntity>
 *
 * @method void add(KdotEntity $entity)
 * @method void set(string $key, KdotEntity $entity)
 * @method KdotEntity[] getIterator()
 * @method KdotEntity[] getElements()
 * @method KdotEntity|null get(string $key)
 * @method KdotEntity|null first()
 * @method KdotEntity|null last()
 */
class KdotCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KdotEntity::class;
    }
}
