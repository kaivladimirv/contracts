<?php

declare(strict_types=1);

namespace App\Service\Sender\Mail;

use Override;
use App\Service\Sender\Message;
use App\Service\Sender\RecipientInterface;
use App\Service\Sender\SenderInterface;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailSender implements SenderInterface
{
    /**
     * @throws Exception
     */
    #[Override]
    public function send(Message $message, RecipientInterface $recipient): void
    {
        /** @var MailRecipientInterface $recipient */

        $mail = new PHPMailer(true);

        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = false;

        $mail->Host = getenv('MAIL_HOST');
        $mail->Username = getenv('MAIL_USERNAME');
        $mail->Password = getenv('MAIL_APP_PASSWORD');
        $mail->Port = (int) getenv('MAIL_PORT');

        $mail->setFrom(getenv('MAIL_FROM'));
        $mail->addAddress($recipient->getEmail());

        $mail->isHTML();
        $mail->Subject = $message->getSubject();
        $mail->Body = $message->getContent();
        $mail->AltBody = $message->getContent();

        $mail->send();
    }
}
