<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ProvidedService;

use App\Model\Contract\Entity\ContractService\ServiceId;
use InvalidArgumentException;

readonly class Service
{
    private string $name;
    private float $quantity;
    private float $price;
    private float $amount;

    public function __construct(private ServiceId $id, string $name, float $quantity, float $price, float $amount)
    {
        $this->assertNameIsNotEmpty($name);
        $this->assertQuantityIsGreaterThanZero($quantity);
        $this->assertPriceIsGreaterThanZero($price);
        $this->assertAmountIsGreaterThanZero($amount);

        if ($quantity * $price !== $amount) {
            throw new InvalidArgumentException('Некорректно указана сумма');
        }
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->amount = $amount;
    }

    public function getId(): ServiceId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    private function assertNameIsNotEmpty(string $name): void
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Не указано название услуги');
        }
    }

    private function assertQuantityIsGreaterThanZero(float $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Количество должно быть больше нуля');
        }
    }

    private function assertPriceIsGreaterThanZero(float $price): void
    {
        if ($price <= 0) {
            throw new InvalidArgumentException('Стоимость должна быть больше нуля');
        }
    }

    private function assertAmountIsGreaterThanZero(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Сумма должна быть больше нуля');
        }
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id->getValue(),
            'name'     => $this->name,
            'quantity' => $this->quantity,
            'price'    => $this->price,
            'amount'   => $this->amount,
        ];
    }
}
