<?php

declare(strict_types=1);

namespace Unit\Model\Contract\Entity\InsuredPerson;

use App\Tests\Builder\Contract\InsuredPersonBuilder;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $insuredPerson = (new InsuredPersonBuilder())->build();
        $insuredPerson->delete();

        self::assertTrue($insuredPerson->isDeleted());
    }
}
