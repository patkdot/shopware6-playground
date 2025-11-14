<?php

declare(strict_types=1);

namespace KdotPlayground\MessageHandler;

use KdotPlayground\Flow\Event\KdotEvent;
use KdotPlayground\Message\KdotUpsertMessage;
use KdotPlayground\Service\KdotService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class KdotUpsertMessageHandler
{
    public function __construct(
        private readonly KdotService $kdotService,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function __invoke(KdotUpsertMessage $message): void
    {
        if ($message->isSync()) {
            $this->kdotService->directUpsertViaSync($message->getUpserts(), $message->getContext());
        } else {
            $this->kdotService->directUpsertViaRepository($message->getUpserts(), $message->getContext());
        }

        /** @var array<string, mixed> $upserts */
        $upserts = $message->getUpserts();
        $this->dispatcher->dispatch(new KdotEvent($upserts, $message->getContext()));
    }
}
