<?php

declare(strict_types=1);

namespace KdotPlayground\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1759358585CreateKdotTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1759358585;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `kdot` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `active` TINYINT(1) COLLATE utf8mb4_unicode_ci,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.kdot.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);

        $translationSql = <<<SQL
CREATE TABLE IF NOT EXISTS `kdot_translation` (
    `kdot_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    `name` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`kdot_id`, `language_id`),
    CONSTRAINT `fk.kdot_translation.kdot_id` FOREIGN KEY (`kdot_id`) 
        REFERENCES `kdot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.kdot_translation.language_id` FOREIGN KEY (`language_id`) 
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($translationSql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
