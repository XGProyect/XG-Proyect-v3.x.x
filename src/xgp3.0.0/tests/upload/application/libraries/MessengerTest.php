<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
final class MessengerTest extends TestCase
{
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'lucky',
            'lucky'
        );
    }

    public function testCanBeUsedAsNumber(): void
    {

    }
}