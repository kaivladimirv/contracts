<?php

declare(strict_types=1);

namespace App\Service\Sender;

use App\Framework\DIContainer\ContainerInterface;
use App\Service\Sender\Mail\MailSender;
use App\Service\Sender\Telegram\TelegramSender;
use UnexpectedValueException;

readonly class SenderFactory
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ContainerInterface $container)
    {
    }

    public function create(int $senderType): SenderInterface
    {
        return match ($senderType) {
            SenderTypes::MAIL => $this->container->get(MailSender::class),
            SenderTypes::TELEGRAM => $this->container->get(TelegramSender::class),
            default => throw new UnexpectedValueException('Неизвестный senderType'),
        };
    }
}
