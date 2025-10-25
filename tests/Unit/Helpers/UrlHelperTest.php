<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\UrlHelper;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class UrlHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up default server variables for testing
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_PORT'] = 80;
    }

    #[Test]
    public function it_returns_empty_string_for_http_protocol_only(): void
    {
        $result = UrlHelper::prepUrl('http://');
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_empty_url(): void
    {
        $result = UrlHelper::prepUrl('');
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_keeps_url_with_http_protocol(): void
    {
        $url = 'http://example.com';
        $result = UrlHelper::prepUrl($url);

        $this->assertEquals($url, $result);
    }

    #[Test]
    public function it_keeps_url_with_https_protocol(): void
    {
        $url = 'https://example.com';
        $result = UrlHelper::prepUrl($url);

        $this->assertEquals($url, $result);
    }

    #[Test]
    public function it_adds_protocol_to_url_without_protocol(): void
    {
        $url = 'example.com';
        $result = UrlHelper::prepUrl($url);

        $this->assertEquals('http://example.com', $result);
    }

    #[Test]
    public function it_adds_https_protocol_when_https_is_on(): void
    {
        $_SERVER['HTTPS'] = 'on';

        $url = 'example.com';
        $result = UrlHelper::prepUrl($url);

        $this->assertEquals('https://example.com', $result);
    }

    #[Test]
    public function it_adds_https_protocol_when_port_is_443(): void
    {
        $_SERVER['SERVER_PORT'] = 443;

        $url = 'example.com';
        $result = UrlHelper::prepUrl($url);

        $this->assertEquals('https://example.com', $result);
    }

    #[Test]
    public function it_creates_anchor_tag_with_basic_parameters(): void
    {
        $hyperlink = 'http://example.com';
        $text = 'Click here';

        $result = UrlHelper::setUrl($hyperlink, $text);

        $this->assertEquals('<a href="http://example.com"   >Click here</a>', $result);
    }

    #[Test]
    public function it_creates_anchor_tag_with_title(): void
    {
        $hyperlink = 'http://example.com';
        $text = 'Click here';
        $title = 'Example Site';

        $result = UrlHelper::setUrl($hyperlink, $text, $title);

        $this->assertEquals('<a href="http://example.com" title="Example Site"  >Click here</a>', $result);
    }

    #[Test]
    public function it_creates_anchor_tag_with_attributes(): void
    {
        $hyperlink = 'http://example.com';
        $text = 'Click here';
        $title = 'Example Site';
        $attributes = 'target="_blank" rel="noopener"';

        $result = UrlHelper::setUrl($hyperlink, $text, $title, $attributes);

        $this->assertEquals('<a href="http://example.com" title="Example Site"  target="_blank" rel="noopener">Click here</a>', $result);
    }

    #[Test]
    public function it_uses_hash_for_empty_hyperlink(): void
    {
        $hyperlink = '';
        $text = 'Click here';

        $result = UrlHelper::setUrl($hyperlink, $text);

        $this->assertEquals('<a href="#"   >Click here</a>', $result);
    }

    #[Test]
    public function it_creates_anchor_without_title_when_empty(): void
    {
        $hyperlink = 'http://example.com';
        $text = 'Click here';
        $title = '';

        $result = UrlHelper::setUrl($hyperlink, $text, $title);

        $this->assertStringNotContainsString('title=', $result);
    }

    #[Test]
    public function it_gets_http_protocol_by_default(): void
    {
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_PORT'] = 80;

        $result = UrlHelper::getUrlProtocol();

        $this->assertEquals('http://', $result);
    }

    #[Test]
    public function it_gets_https_protocol_when_https_is_on(): void
    {
        $_SERVER['HTTPS'] = 'on';

        $result = UrlHelper::getUrlProtocol();

        $this->assertEquals('https://', $result);
    }

    #[Test]
    public function it_gets_https_protocol_when_https_is_not_off(): void
    {
        $_SERVER['HTTPS'] = '1';

        $result = UrlHelper::getUrlProtocol();

        $this->assertEquals('https://', $result);
    }

    #[Test]
    public function it_gets_https_protocol_when_port_is_443(): void
    {
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_PORT'] = 443;

        $result = UrlHelper::getUrlProtocol();

        $this->assertEquals('https://', $result);
    }

    #[Test]
    #[DataProvider('urlProtocolProvider')]
    public function it_detects_correct_protocol(string $https, int $port, string $expected): void
    {
        $_SERVER['HTTPS'] = $https;
        $_SERVER['SERVER_PORT'] = $port;

        $result = UrlHelper::getUrlProtocol();

        $this->assertEquals($expected, $result);
    }

    public static function urlProtocolProvider(): array
    {
        return [
            'http default' => ['off', 80, 'http://'],
            'https on' => ['on', 80, 'https://'],
            'https port 443' => ['off', 443, 'https://'],
            'https both' => ['on', 443, 'https://'],
            'https value 1' => ['1', 80, 'https://'],
        ];
    }
}
