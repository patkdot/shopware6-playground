<?php

declare(strict_types=1);

namespace KdotPlayground\Flow\Action;

use KdotPlayground\Flow\Aware\KdotAware;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Mail\Service\AbstractMailService;

class KdotAction extends FlowAction
{
    public function __construct(
        private readonly AbstractMailService $mailService,
    ) {
    }

    public static function getName(): string
    {
        return 'action.kdot';
    }

    public function requirements(): array
    {
        return [KdotAware::class];
    }

    public function handleFlow(StorableFlow $flow): void
    {
        if (!$flow->hasStore('kdot')) {
            return;
        }

        // https://muellmail.com/#/kdot@schafmail.de
        $this->mailService->send([
            'subject' => 'Kdot',
            'contentHtml' => 'Kdot',
            'contentPlain' => 'Kdot',
            'recipients' => ['kdot@schlafmail.de' => 'Kdot'],
            'senderName' => 'Kdot',
            'senderMail' => 'kdot@schlafmail.de',
            'salesChannelId' => '0199cf2ca2617322b3a073ddb5a72e14',
        ], $flow->getContext());
    }
}
