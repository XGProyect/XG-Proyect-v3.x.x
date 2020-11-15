<?php declare (strict_types = 1);

/**
 * URL Helper
 *
 * @category Helper
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\helpers;

/**
 * UrlHelper Class
 */
abstract class UrlHelper
{
    /**
     * Prepare the URL to have the right protocol set
     *
     * @param string $url
     * @return string
     */
    public static function prepUrl(string $url = ''): string
    {
        if ($url == 'http://' or $url == '') {
            return '';
        }

        if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
            $url = self::getUrlProtocol() . $url;
        }

        return $url;
    }

    /**
     * Set a new anchor HTML tag with the provided data
     *
     * @param string $hyperlink
     * @param string $text
     * @param string $title
     * @param string $attributes
     * @return string
     */
    public static function setUrl(string $hyperlink, string $text, string $title = '', string $attributes = ''): string
    {
        if (empty($hyperlink)) {
            $hyperlink = '#';
        }

        if (!empty($title)) {
            $title = 'title="' . $title . '"';
        }

        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        return '<a href="' . $hyperlink . '" ' . $title . ' ' . $attributes . '>' . $text . '</a>';
    }

    /**
     * Get the site current protocol
     *
     * @return string
     */
    public static function getUrlProtocol(): string
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' or $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }
}
