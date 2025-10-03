<?php declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use KdotPlayground\Core\Content\Kdot\Aggregate\KdotTranslationCollection;

class KdotEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $productId;

    protected ?string $name;

    protected ?string $description;

    protected bool $active;

    protected ?KdotTranslationCollection $translations = null;

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTranslations(): ?KdotTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(KdotTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}
