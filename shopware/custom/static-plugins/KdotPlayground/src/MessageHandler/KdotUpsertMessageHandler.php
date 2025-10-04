<?php

declare(strict_types=1);

namespace KdotPlayground\MessageHandler;

use KdotPlayground\Message\KdotUpsertMessage;
use KdotPlayground\Service\KdotService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class KdotUpsertMessageHandler
{
    public function __construct(
        private readonly KdotService $kdotService,
    ) {
    }

    public function __invoke(KdotUpsertMessage $message)
    {
        if ($message->isSync()) {
            $this->kdotService->directUpsertViaSync($message->getUpserts(), $message->getContext());
        } else {
            $this->kdotService->directUpsertViaRepository($message->getUpserts(), $message->getContext());
        }
    }
}