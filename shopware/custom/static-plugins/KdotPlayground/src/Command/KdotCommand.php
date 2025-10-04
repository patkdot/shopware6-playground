<?php

declare(strict_types=1);

namespace KdotPlayground\Command;

use KdotPlayground\Service\KdotService;
use KdotPlayground\Service\ProductService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'kdot:import',
    description: 'kdot import command',
)]
class KdotCommand extends Command
{
    public function __construct(
        private readonly KdotService $kdotService,
        private readonly ProductService $productService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('fast', 'f', InputOption::VALUE_NONE, 'Fast import with raw sql and sync service')
            ->addOption('use-queue', 'queue', InputOption::VALUE_NONE, 'Use queue for upserts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $useQueue = $input->getOption('use-queue');
        $fast = $input->getOption('fast');
        $context = Context::createDefaultContext();

        if ($fast) {
            $allProducts = $this->productService->getAllProductsViaRawSql($context);
            $upsertsCount = $this->kdotService->upsertFromProductCollectionViaSync($allProducts, $context, $useQueue);
        } else {
            $allProducts = $this->productService->getAllProducts();
            $upsertsCount = $this->kdotService->upsertFromProductCollectionViaRepository($allProducts, $context, $useQueue);
        }

        $message = 'Kdot imported ' . $upsertsCount . ' elements successfully';

        if ($useQueue) {
            $message .= ' into the queue';
        }
        $output->writeln($message);

        return Command::SUCCESS;
    }
}
