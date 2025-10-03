<?php declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use KdotPlayground\Core\Content\Kdot\KdotDefinition;
use KdotPlayground\Core\Content\Kdot\KdotTranslationEntity;
use KdotPlayground\Core\Content\Kdot\KdotTranslationCollection;

class KdotTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'kdot_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return KdotDefinition::class;
    }

    public function getEntityClass(): string
    {
        return KdotTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return KdotTranslationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new StringField('name', 'name'),
            new StringField('description', 'description'),
        ]);
    }
}