<?php

declare(strict_types=1);

namespace App\ReadModel\ProvidedService\Dto;

readonly class ExpenseDto
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private float $quantity, private float $amount)
    {
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'amount'   => $this->amount,
        ];
    }
}
