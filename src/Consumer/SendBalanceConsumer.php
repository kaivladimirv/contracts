<?php

declare(strict_types=1);

namespace App\Consumer;

use Override;
use App\Framework\Console\ConsoleInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Repository\PersonRepositoryInterface;
use App\ReadModel\Contract\Balance\BalanceFetcherInterface;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoCollection;
use App\Service\Queue\ConsumerInterface;
use App\Service\Queue\QueueMessage;
use App\Service\Sender\Mail\MailRecipient;
use App\Service\Sender\Message;
use App\Service\Sender\RecipientInterface;
use App\Service\Sender\SenderFactory;
use App\Service\Sender\SenderTypes;
use App\Service\Sender\Telegram\TelegramRecipient;
use Exception;
use UnexpectedValueException;

readonly class SendBalanceConsumer implements ConsumerInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ConsoleInterface $console, private SenderFactory $senderFactory, private InsuredPersonRepositoryInterface $insuredPersonRepository, private PersonRepositoryInterface $personRepository, private BalanceFetcherInterface $balanceFetcher)
    {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function consume(QueueMessage $message): void
    {
        $msg = json_decode((string) $message->getBody(), false);

        $insuredPersonId = new InsuredPersonId($msg->insuredPersonId);
        $person = $this->getPerson($insuredPersonId);

        if ($person->shouldBeNotified()) {
            $balances = $this->balanceFetcher->getAllByInsuredPersonId($insuredPersonId);

            $sender = $this->senderFactory->create($person->getNotifierType()->getValue());

            $sender->send(
                $this->buildMessage($balances),
                $this->buildRecipient($person)
            );

            $this->console->success(
                'Информация об остатках отправлена застрахованному лицу ' . $person->getName()->getFullName()
            );
        }
    }

    /**
     * @throws PersonNotFoundException
     * @throws InsuredPersonNotFoundException
     */
    private function getPerson(InsuredPersonId $insuredPersonId): Person
    {
        $insuredPerson = $this->insuredPersonRepository->getOne($insuredPersonId);

        return $this->personRepository->getOne(new PersonId($insuredPerson->getPersonId()->getValue()));
    }

    private function buildMessage(BalanceDtoCollection $balances): Message
    {
        $lines = ['Ваши остатки по услугам:'];

        foreach ($balances as $balance) {
            $lines[] = sprintf("%s: %s", $balance->serviceName, $balance->balance);
        }

        $text = implode(PHP_EOL, $lines);

        return new Message('Остатки по услугам', $text);
    }

    private function buildRecipient(Person $person): RecipientInterface
    {
        return match ($person->getNotifierType()->getValue()) {
            SenderTypes::MAIL => new MailRecipient($person->getEmail()->getValue()),
            SenderTypes::TELEGRAM => new TelegramRecipient(
                !empty($person->getPhoneNumber()) ? $person->getPhoneNumber()->getValue() : '',
                !empty($person->getTelegramUserId()) ? $person->getTelegramUserId() : '',
                $person->getName()->getFirstName(),
                $person->getName()->getLastName()
            ),
            default => throw new UnexpectedValueException('Неизвестный NotifierType'),
        };
    }
}
