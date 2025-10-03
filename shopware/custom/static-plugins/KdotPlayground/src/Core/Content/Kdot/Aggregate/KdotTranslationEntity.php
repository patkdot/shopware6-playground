<?php declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use KdotPlayground\Core\Content\Kdot\KdotEntity;

class KdotTranslationEntity extends TranslationEntity
{
    use EntityIdTrait;

    protected ?string $kdotId;
    protected ?string $name;
    protected ?string $description;

    public function getKdotId(): string
    {
        return $this->kdotId;
    }

    public function setKdotId(string $kdotId): void
    {
        $this->kdotId = $kdotId;
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

    public function getKdot(): KdotEntity
    {
        return $this->kdot;
    }

    public function setKdot(?KdotEntity $kdot): void
    {
        $this->kdot = $kdot;
    }
}