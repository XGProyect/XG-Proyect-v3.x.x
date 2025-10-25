<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\StringsHelper;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class StringsHelperTest extends TestCase
{
    #[Test]
    public function it_generates_random_string_with_default_keyspace(): void
    {
        $length = 16;
        $result = StringsHelper::randomString($length);

        $this->assertIsString($result);
        $this->assertEquals($length, strlen($result));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $result);
    }

    #[Test]
    public function it_generates_random_string_with_custom_keyspace(): void
    {
        $length = 10;
        $keyspace = '0123456789';
        $result = StringsHelper::randomString($length, $keyspace);

        $this->assertIsString($result);
        $this->assertEquals($length, strlen($result));
        $this->assertMatchesRegularExpression('/^[0-9]+$/', $result);
    }

    #[Test]
    public function it_generates_unique_random_strings(): void
    {
        $length = 20;
        $results = [];

        // Generate 100 random strings
        for ($i = 0; $i < 100; $i++) {
            $results[] = StringsHelper::randomString($length);
        }

        // All should be unique
        $unique = array_unique($results);
        $this->assertCount(100, $unique);
    }

    #[Test]
    public function it_throws_exception_for_invalid_keyspace(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$keyspace must be at least two characters long');

        StringsHelper::randomString(10, 'a');
    }

    #[Test]
    public function it_throws_exception_for_empty_keyspace(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$keyspace must be at least two characters long');

        StringsHelper::randomString(10, '');
    }

    #[Test]
    #[DataProvider('escapeStringProvider')]
    public function it_escapes_string_correctly(string $input, string $expected): void
    {
        $result = StringsHelper::escapeString($input);
        $this->assertEquals($expected, $result);
    }

    public static function escapeStringProvider(): array
    {
        return [
            'backslash' => ['test\\string', 'test\\\\string'],
            'null byte' => ["test\x00string", "test\\0string"],
            'newline' => ["test\nstring", "test\\nstring"],
            'carriage return' => ["test\rstring", "test\\rstring"],
            'single quote' => ["test'string", "test\\'string"],
            'double quote' => ['test"string', 'test\\"string'],
            'substitute character' => ["test\x1astring", "test\\Zstring"],
            'no special chars' => ['test string', 'test string'],
            'multiple special chars' => ["test'\\\"string", "test\\'\\\\\\\"string"],
        ];
    }

    #[Test]
    public function it_parses_replacements_with_single_placeholder(): void
    {
        $text = 'Hello %s!';
        $replacements = ['World'];

        $result = StringsHelper::parseReplacements($text, $replacements);

        $this->assertEquals('Hello World!', $result);
    }

    #[Test]
    public function it_parses_replacements_with_multiple_placeholders(): void
    {
        $text = 'User %s has %d points and level %d';
        $replacements = ['John', 100, 5];

        $result = StringsHelper::parseReplacements($text, $replacements);

        $this->assertEquals('User John has 100 points and level 5', $result);
    }

    #[Test]
    public function it_parses_replacements_with_no_placeholders(): void
    {
        $text = 'No placeholders here';
        $replacements = [];

        $result = StringsHelper::parseReplacements($text, $replacements);

        $this->assertEquals('No placeholders here', $result);
    }

    #[Test]
    public function it_handles_empty_string(): void
    {
        $text = '';
        $replacements = [];

        $result = StringsHelper::parseReplacements($text, $replacements);

        $this->assertEquals('', $result);
    }
}
