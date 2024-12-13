<?php

declare(strict_types=1);

namespace App\Command;

use danog\MadelineProto\Tools;
use Override;
use App\Framework\Command\AbstractCommand;
use danog\MadelineProto\API;

/**
 * @psalm-api
 */
class TelegramLoginCommand extends AbstractCommand
{
    public function __construct(private readonly API $madeline)
    {
    }

    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        $this->madeline->phoneLogin('+' . getenv('TELEGRAM_PHONE_NUMBER'));

        $code = Tools::readLine('Введите код подтверждения: ');

        $this->madeline->completePhoneLogin($code);

        $this->console->info('OK');
    }
}
