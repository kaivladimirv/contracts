<?php

declare(strict_types=1);

namespace App\ReadModel\ProvidedService\Dto;

use App\Service\Hydrator\HydratorInterface;

readonly class ExpenseDtoConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToDto(array $data): ExpenseDto
    {
        $data = [
            'quantity' => isset($data['quantity']) ? (float) $data['quantity'] : 0,
            'amount'   => isset($data['amount']) ? (float) $data['amount'] : 0,
        ];

        return $this->hydrator->hydrate(ExpenseDto::class, $data);
    }
}
