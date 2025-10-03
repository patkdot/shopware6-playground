<?php

declare(strict_types=1);

namespace KdotPlayground\Command;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'kdot:import',
    description: 'kdot import command',
)]
class KdotCommand extends Command
{
    /**
     * @param EntityRepository<KdotCollection> $kdotRepository
     * @param EntityRepository<ProductCollection> $productRepository
     */
    public function __construct(
        private readonly EntityRepository $kdotRepository,
        private EntityRepository $productRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $criteria = new Criteria();
        $criteria->addAssociation('kdot');
        $allProducts = $this->productRepository->search($criteria, Context::createDefaultContext())->getEntities();
        $upserts = [];
        /** @var ProductEntity $product */
        foreach ($allProducts as $product) {
            if ($product->getExtension('kdot')->count() > 0) {
                continue;
            }
            $upserts[] = [
                'id' => Uuid::randomHex(),
                'translations' => [
                    'de-DE' => ['name' => 'Kdot DE ' . $product->getName(), 'description' => 'Kdot DE ' . $product->getDescription()],
                    'en-GB' => ['name' => 'Kdot EN ' . $product->getName(), 'description' => 'Kdot EN ' . $product->getDescription()],
                ],
                'active' => true,
                'productId' => $product->getId(),
            ];
        }

        $this->kdotRepository->upsert($upserts, Context::createDefaultContext());

        $output->writeln('Kdot imported ' . count($upserts) . ' elements successfully');

        return Command::SUCCESS;
    }
}
