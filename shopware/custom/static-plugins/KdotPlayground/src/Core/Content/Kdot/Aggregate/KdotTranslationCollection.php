<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<KdotTranslationEntity>
 *
 * @method void add(KdotTranslationEntity $entity)
 * @method void set(string $key, KdotTranslationEntity $entity)
 * @method KdotTranslationEntity[] getIterator()
 * @method KdotTranslationEntity[] getElements()
 * @method KdotTranslationEntity|null get(string $key)
 * @method KdotTranslationEntity|null first()
 * @method KdotTranslationEntity|null last()
 */
class KdotTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KdotTranslationEntity::class;
    }
}
