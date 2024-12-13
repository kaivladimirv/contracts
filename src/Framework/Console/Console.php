<?php

declare(strict_types=1);

namespace App\Framework\Console;

use Override;
use App\Framework\Console\Argument\ArgumentInterface;
use App\Framework\Console\Argument\Arguments;
use App\Framework\Console\ExpectedArgument\ExpectedArgument;
use Exception;

class Console implements ConsoleInterface
{
    private const string COLOR__GREEN = '32m';
    private const string COLOR__RED   = '31m';

    public function __construct(private readonly Arguments $arguments)
    {
    }

    #[Override]
    public function success($message): void
    {
        $message = $this->setColorFor(self::COLOR__GREEN, $message);

        $this->writeLn($message);
    }

    #[Override]
    public function error($message): void
    {
        $message = $this->setColorFor(self::COLOR__RED, $message);

        $this->writeLn($message);
    }

    #[Override]
    public function info($message): void
    {
        $this->writeLn($message);
    }

    #[Override]
    public function setColorFor(string $color, $text): string
    {
        return "\033[$color$text\033[0m";
    }

    private function writeLn($message): void
    {
        $this->write($message . PHP_EOL);
    }

    private function write($message): void
    {
        echo $message;
    }

    #[Override]
    public function readLines(): array
    {
        stream_set_blocking(STDIN, false);

        $data = [];

        while (!feof(STDIN)) {
            if (false === $value = fgets(STDIN)) {
                break;
            }
            $data[] = rtrim($value);
        }

        return $data;
    }

    #[Override]
    public function readLine(): string
    {
        return rtrim(fgets(STDIN));
    }

    #[Override]
    public function addExpectedArgument(ExpectedArgument $expectedArgument): void
    {
        $this->arguments->addExpectedArgument($expectedArgument);
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function getFirstArgument(): ArgumentInterface
    {
        return $this->arguments->getFirst();
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function getArgumentByNumber(int $argumentNumber): ArgumentInterface
    {
        return $this->arguments->getByNumber($argumentNumber);
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function getArgumentByName(string $argumentName): ArgumentInterface
    {
        return $this->arguments->getByName($argumentName);
    }
}
