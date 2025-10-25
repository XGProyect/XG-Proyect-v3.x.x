<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\ArraysHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class ArraysHelperTest extends TestCase
{
    #[Test]
    public function it_finds_value_in_flat_array(): void
    {
        $haystack = ['apple', 'banana', 'cherry'];

        $this->assertTrue(ArraysHelper::inMultiArray('banana', $haystack));
        $this->assertFalse(ArraysHelper::inMultiArray('grape', $haystack));
    }

    #[Test]
    public function it_finds_value_in_nested_array(): void
    {
        $haystack = [
            'fruits' => ['apple', 'banana'],
            'vegetables' => ['carrot', 'potato'],
        ];

        $this->assertTrue(ArraysHelper::inMultiArray('banana', $haystack));
        $this->assertTrue(ArraysHelper::inMultiArray('carrot', $haystack));
        $this->assertFalse(ArraysHelper::inMultiArray('grape', $haystack));
    }

    #[Test]
    public function it_finds_value_in_deeply_nested_array(): void
    {
        $haystack = [
            'level1' => [
                'level2' => [
                    'level3' => ['target'],
                ],
            ],
        ];

        $this->assertTrue(ArraysHelper::inMultiArray('target', $haystack));
        $this->assertFalse(ArraysHelper::inMultiArray('missing', $haystack));
    }

    #[Test]
    public function it_returns_false_for_empty_array(): void
    {
        $haystack = [];

        $this->assertFalse(ArraysHelper::inMultiArray('anything', $haystack));
    }

    #[Test]
    public function it_handles_numeric_string_values(): void
    {
        $haystack = ['1', '2', '3'];

        $this->assertTrue(ArraysHelper::inMultiArray('2', $haystack));
        $this->assertFalse(ArraysHelper::inMultiArray('4', $haystack));
    }

    #[Test]
    public function it_searches_and_returns_key_in_flat_array(): void
    {
        $haystack = ['apple', 'banana', 'cherry'];

        $result = ArraysHelper::multiArraySearch('banana', $haystack);

        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_searches_and_returns_key_in_nested_array(): void
    {
        $haystack = [
            10 => 'first',
            20 => ['nested_value'],
            30 => 'third',
        ];

        $result = ArraysHelper::multiArraySearch('nested_value', $haystack);

        $this->assertEquals(20, $result);
    }

    #[Test]
    public function it_returns_null_when_value_not_found(): void
    {
        $haystack = ['apple', 'banana', 'cherry'];

        $result = ArraysHelper::multiArraySearch('grape', $haystack);

        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_first_key_when_value_found(): void
    {
        $haystack = [
            0 => 'test',
            1 => 'value',
            2 => 'test',  // duplicate
        ];

        $result = ArraysHelper::multiArraySearch('test', $haystack);

        $this->assertEquals(0, $result);
    }

    #[Test]
    public function it_searches_in_deeply_nested_array(): void
    {
        $haystack = [
            5 => [
                'level2' => [
                    'target',
                ],
            ],
        ];

        $result = ArraysHelper::multiArraySearch('target', $haystack);

        $this->assertEquals(5, $result);
    }

    #[Test]
    public function it_returns_null_for_empty_array_search(): void
    {
        $haystack = [];

        $result = ArraysHelper::multiArraySearch('anything', $haystack);

        $this->assertNull($result);
    }

    #[Test]
    public function it_uses_strict_comparison_for_search(): void
    {
        $haystack = ['1', '2', '3'];

        // Strict comparison: '1' === '1' should work
        $result = ArraysHelper::multiArraySearch('1', $haystack);
        $this->assertEquals(0, $result);
    }

    #[Test]
    public function it_handles_mixed_nested_structures(): void
    {
        $haystack = [
            'users' => [
                'admin' => ['Alice', 'Bob'],
                'guest' => 'Charlie',
            ],
            'settings' => ['active'],
        ];

        $this->assertTrue(ArraysHelper::inMultiArray('Alice', $haystack));
        $this->assertTrue(ArraysHelper::inMultiArray('Charlie', $haystack));
        $this->assertTrue(ArraysHelper::inMultiArray('active', $haystack));
        $this->assertFalse(ArraysHelper::inMultiArray('David', $haystack));
    }
}
