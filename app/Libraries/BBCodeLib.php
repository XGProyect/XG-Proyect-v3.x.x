<?php

namespace App\Libraries;

use App\Helpers\UrlHelper;

class BBCodeLib
{
    public function bbCode(?string $string = ''): string
    {
        $bbcodes = [
            '/\\n/' => '$this->setLineJump()',
            '/\\r/' => '$this->setReturn()',
            '/\[list\](.*?)\[\/list\]/is' => '$this->setList(\'\\1\')',
            '/\[b\](.*?)\[\/b\]/is' => '$this->setBold(\'\\1\')',
            '/\[strong\](.*?)\[\/strong\]/is' => '$this->setBold(\'\\1\')',
            '/\[i\](.*?)\[\/i\]/is' => '$this->setItalic(\'\\1\')',
            '/\[u\](.*?)\[\/u\]/is' => '$this->setUnderline(\'\\1\')',
            '/\[s\](.*?)\[\/s\]/is' => '$this->setStrike(\'\\1\')',
            '/\[del\](.*?)\[\/del\]/is' => '$this->setStrike(\'\\1\')',
            '/\[url=(.*?)\](.*?)\[\/url\]/is' => '$this->setUrl(\'\\1\',\'\\2\')',
            '/\[email=(.*?)\](.*?)\[\/email\]/is' => '$this->setEmail(\'\\1\',\'\\2\')',
            '/\[img](.*?)\[\/img\]/is' => '$this->setImage(\'\\1\')',
            '/\[color=(.*?)\](.*?)\[\/color\]/is' => '$this->setFontColor(\'\\1\',\'\\2\')',
            '/\[font=(.*?)\](.*?)\[\/font\]/is' => '$this->setFontFamiliy(\'\\1\',\'\\2\')',
            '/\[bg=(.*?)\](.*?)\[\/bg\]/is' => '$this->setBackgroundColor(\'\\1\',\'\\2\')',
            '/\[size=(.*?)\](.*?)\[\/size\]/is' => '$this->setFontSize(\'\\1\',\'\\2\')',
            '/\[coordinates\](.*?):(.*?):(.*?)\[\/coordinates]/is' => '$this->setCoordinates(\'\\1\',\'\\2\',\'\\3\')',
        ];

        $string = stripslashes($string ?? '');

        foreach ($bbcodes as $bbcode => $html) {
            $string = preg_replace_callback(
                $bbcode,
                function ($matches) use ($html) {
                    return $this->getBbCode($matches, $html);
                },
                $string
            );
        }

        return $string;
    }

    private function getBbCode(array $matches, string $replace): string
    {
        if (isset($matches[1])) {
            $replacements = [
                '\1' => isset($matches[1]) ? $matches[1] : '',
                '\2' => isset($matches[2]) ? $matches[2] : '',
                '\3' => isset($matches[3]) ? $matches[3] : '',
            ];

            return eval('return ' . strtr($replace, $replacements) . ';');
        } else {
            return eval('return ' . $replace . ';');
        }
    }

    private function setLineJump(): string
    {
        return '<br/>';
    }

    private function setReturn(): string
    {
        return '';
    }

    private function setList(mixed $string): string
    {
        $tmp = explode('[*]', stripslashes($string));
        $out = null;

        foreach ($tmp as $list) {
            if (strlen(str_replace('', '', $list)) > 0) {
                $out .= '<li>' . trim($list) . '</li>';
            }
        }

        return '<ul>' . $out . '</ul>';
    }

    private function setBold(string $string): string
    {
        return '<span style="font-weight: bold;">' . stripslashes($string) . '</span>';
    }

    private function setItalic(string $string): string
    {
        return '<span style="font-style: italic;">' . stripslashes($string) . '</span>';
    }

    private function setUnderline(string $string): string
    {
        return '<span style="text-decoration: underline;">' . stripslashes($string) . '</span>';
    }

    private function setStrike(string $string): string
    {
        return '<span style="text-decoration: line-through;">' . stripslashes($string) . '</span>';
    }

    private function setUrl(string $url, string $title): ?string
    {
        $title = htmlspecialchars(stripslashes($title), ENT_QUOTES);
        $url = trim($url);
        $exclude = [
            'data', 'file', 'javascript', 'jar', '#',
        ];

        if (in_array(strstr($url, ':', true), $exclude) == false) {
            return UrlHelper::setUrl($url, $title, $title);
        }

        return $url;
    }

    private function setEmail(string $mail, string $title): string
    {
        return '<a href="mailto:' . $mail . '" title="' . $mail . '">' . stripslashes($title) . '</a>';
    }

    private function setImage(string $img): string
    {
        if ((substr($img, 0, 7) != 'http://') && (substr($img, 0, 8) != 'https://')) {
            $img = XGP_ROOT . IMG_PATH . $img;
        }

        return '<img src="' . $img . '" alt="' . $img . '" title="' . $img . '" />';
    }

    private function setFontColor(string $color, string $title): string
    {
        return '<span style="color:' . $color . '">' . stripslashes($title) . '</span>';
    }

    private function setFontFamiliy(string $font, string $title): string
    {
        return '<span style="font-family:' . $font . '">' . stripslashes($title) . '</span>';
    }

    private function setBackgroundColor(string $bg, string $title): string
    {
        return '<span style="background-color:' . $bg . '">' . stripslashes($title) . '</span>';
    }

    private function setFontSize(string $size, string $text): string
    {
        return '<span style="font-size:' . $size . 'px">' . stripslashes($text) . '</span>';
    }

    private function setCoordinates($galaxy, $system, $planet)
    {
        return FormatLib::prettyCoords($galaxy, $system, $planet);
    }
}
