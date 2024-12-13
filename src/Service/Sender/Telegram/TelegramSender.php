<?php

declare(strict_types=1);

namespace App\Service\Sender\Telegram;

use Override;
use App\Service\Sender\Message;
use App\Service\Sender\RecipientInterface;
use App\Service\Sender\SenderInterface;
use danog\MadelineProto\API;

readonly class TelegramSender implements SenderInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private API $madeline)
    {
    }

    #[Override]
    public function send(Message $message, RecipientInterface $recipient): void
    {
        /** @var TelegramRecipientInterface $recipient */

        if ($recipient->getUserId()) {
            $userId = $recipient->getUserId();
        } else {
            $userId = $this->addRecipientToTelegramContactList($recipient);
        }

        $this->madeline->messages->sendMessage(
            peer: $userId,
            message: $message->getContent()
        );
    }

    private function addRecipientToTelegramContactList(TelegramRecipientInterface $recipient): string
    {
        $contact = [
            '_' => 'inputPhoneContact',
            'client_id' => 0,
            'phone' => '+' . $recipient->getPhoneNumber(),
            'first_name' => $recipient->getFirstName(),
            'last_name' => $recipient->getLastName(),
        ];

        $result = $this->madeline->contacts->importContacts(['contacts' => [$contact]]);

        return strval($result['imported'][0]['user_id']);
    }
}
