<?php

declare(strict_types=1);

namespace KdotPlayground;

use KdotPlayground\Installer\CustomFieldsInstaller;
use Psr\Container\ContainerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class KdotPlayground extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $installer = $this->getCustomFieldsInstaller();

        if ($installer !== null) {
            $installer->install($installContext->getContext());
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $installer = $this->getCustomFieldsInstaller();

        if ($installer !== null) {
            $installer->addRelations($activateContext->getContext());
        }
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
    }

    public function update(UpdateContext $updateContext): void
    {
    }

    public function postInstall(InstallContext $installContext): void
    {
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
    }

    private function getCustomFieldsInstaller(): ?CustomFieldsInstaller
    {
        $container = $this->container;

        if (!$container instanceof ContainerInterface) {
            return null;
        }

        if ($container->has(CustomFieldsInstaller::class)) {
            $installer = $container->get(CustomFieldsInstaller::class);

            return $installer instanceof CustomFieldsInstaller ? $installer : null;
        }

        if (!$container->has('custom_field_set.repository') || !$container->has('custom_field_set_relation.repository')) {
            return null;
        }

        $customFieldSetRepository = $container->get('custom_field_set.repository');
        $customFieldSetRelationRepository = $container->get('custom_field_set_relation.repository');

        if (!$customFieldSetRepository instanceof EntityRepository || !$customFieldSetRelationRepository instanceof EntityRepository) {
            return null;
        }

        return new CustomFieldsInstaller($customFieldSetRepository, $customFieldSetRelationRepository);
    }
}
