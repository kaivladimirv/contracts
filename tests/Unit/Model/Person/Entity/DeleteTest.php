<?php

declare(strict_types=1);

namespace Unit\Model\Person\Entity;

use App\Tests\Builder\Person\PersonBuilder;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $person = (new PersonBuilder())->build();

        $person->delete();

        self::assertTrue($person->isDeleted());
    }
}
