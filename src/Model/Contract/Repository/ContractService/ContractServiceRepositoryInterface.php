<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceCollection;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

interface ContractServiceRepositoryInterface
{
    public function getAll(ContractId $contractId): ContractServiceCollection;

    public function get(ContractId $contractId, int $limit, int $skip): ContractServiceCollection;

    /**
     * @throws ContractServiceNotFoundException
     */
    public function getOne(ContractId $contractId, ServiceId $serviceId): ContractService;

    public function add(ContractService $contractService): void;

    public function update(ContractService $contractService): void;

    public function delete(ContractService $contractService): void;
}
