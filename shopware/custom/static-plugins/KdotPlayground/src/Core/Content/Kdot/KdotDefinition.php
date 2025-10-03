<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use KdotPlayground\Core\Content\Kdot\Aggregate\KdotTranslationDefinition;

class KdotDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'kdot';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return KdotEntity::class;
    }

    public function getCollectionClass(): string
    {
        return KdotCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('name')),
            (new TranslatedField('description')),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(
                KdotTranslationDefinition::class,
                'kdot_id'
            ))->addFlags(new Required())
        ]);
    }

    public function getTranslationDefinitionClass(): string
    {
        return KdotTranslationDefinition::class;
    }
}
