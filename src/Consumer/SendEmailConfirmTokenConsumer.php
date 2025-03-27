<?php

declare(strict_types=1);

namespace App\Consumer;

use Override;
use App\Framework\Console\ConsoleInterface;
use App\Service\Queue\ConsumerInterface;
use App\Service\Queue\QueueMessage;
use App\Service\Sender\Mail\MailRecipient;
use App\Service\Sender\Mail\MailRecipientInterface;
use App\Service\Sender\Message;
use App\Service\Sender\SenderInterface;
use Exception;

readonly class SendEmailConfirmTokenConsumer implements ConsumerInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ConsoleInterface $console, private SenderInterface $sender)
    {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function consume(QueueMessage $message): void
    {
        $msg = json_decode((string) $message->getBody(), false);

        $this->sender->send(
            $this->buildMessage($msg->token),
            $this->buildRecipient($msg->email)
        );

        $this->console->success('Письмо для подтверждения регистрации отправлено на адрес ' . $msg->email);
    }

    private function buildMessage(string $token): Message
    {
        $text = "Ваша компания успешно зарегистрирована.<br>";
        $text .= "Токен подтверждения $token.";

        return new Message('Подтверждение электронного адреса', $text);
    }

    private function buildRecipient(string $email): MailRecipientInterface
    {
        return new MailRecipient($email);
    }
}
