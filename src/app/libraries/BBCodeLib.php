<?php
/**
 * BBCode Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\libraries;

use App\helpers\UrlHelper;

/**
 * BBCodeLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class BBCodeLib
{
    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    public function bbCode($string = '')
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
        ];

        $string = stripslashes($string??'');

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

    /**
     * Recover the different stuff and choose the right one
     *
     * @param array  $matches Matches
     * @param string $replace Replace
     *
     * @return string
     */
    private function getBbCode($matches, $replace)
    {
        if (isset($matches[1])) {
            $replacements = [
                '\1' => isset($matches[1]) ? $matches[1] : '',
                '\2' => isset($matches[2]) ? $matches[2] : '',
            ];

            return eval('return ' . strtr($replace, $replacements) . ';');
        } else {
            return eval('return ' . $replace . ';');
        }
    }

    /**
     * Set la line jump
     *
     * @return string
     */
    private function setLineJump()
    {
        return '<br/>';
    }

    /**
     * Set return
     *
     * @return string
     */
    private function setReturn()
    {
        return '';
    }

    /**
     * Set list for a block of text.
     *
     * @param mixed $string String
     *
     * @return string
     */
    private function setList($string)
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

    /**
     * Set bold for a piece of text.
     *
     * @param mixed $string String
     *
     * @return string
     */
    private function setBold($string)
    {
        return '<span style="font-weight: bold;">' . stripslashes($string) . '</span>';
    }

    /**
     * Set italic for a piece of text.
     *
     * @param mixed $string String
     *
     * @return string
     */
    private function setItalic($string)
    {
        return '<span style="font-style: italic;">' . stripslashes($string) . '</span>';
    }

    /**
     * Set underline for a piece of text.
     *
     * @param mixed $string String
     *
     * @return string
     */
    private function setUnderline($string)
    {
        return '<span style="text-decoration: underline;">' . stripslashes($string) . '</span>';
    }

    /**
     * Set line through for a piece of text.
     *
     * @param mixed $string String
     *
     * @return string
     */
    private function setStrike($string)
    {
        return '<span style="text-decoration: line-through;">' . stripslashes($string) . '</span>';
    }

    /**
     * Set the url for a piece of text.
     *
     * @param mixed $url   Url
     * @param mixed $title Title
     *
     * @return string
     */
    private function setUrl($url, $title)
    {
        $title = htmlspecialchars(stripslashes($title), ENT_QUOTES);
        $url = trim($url);
        $exclude = [
            'data', 'file', 'javascript', 'jar', '#',
        ];

        if (in_array(strstr($url, ':', true), $exclude) == false) {
            return UrlHelper::setUrl($url, $title, $title);
        }
    }

    /**
     * Set an email for a piece of text
     *
     * @param mixed $mail  Mail
     * @param mixed $title Title
     *
     * @return string
     */
    private function setEmail($mail, $title)
    {
        return '<a href="mailto:' . $mail . '" title="' . $mail . '">' . stripslashes($title) . '</a>';
    }

    /**
     * Set an image
     *
     * @param mixed $img Mixed
     *
     * @return void
     */
    private function setImage($img)
    {
        if ((substr($img, 0, 7) != 'http://') && (substr($img, 0, 8) != 'https://')) {
            $img = XGP_ROOT . IMG_PATH . $img;
        }
        return '<img src="' . $img . '" alt="' . $img . '" title="' . $img . '" />';
    }

    /**
     * Set the font color for a piece of text.
     *
     * @param string $color Color
     * @param string $title Title
     *
     * @return string
     */
    private function setFontColor($color, $title)
    {
        return '<span style="color:' . $color . '">' . stripslashes($title) . '</span>';
    }

    /**
     * Set the font family for a piece of text.
     *
     * @param string $font  Font
     * @param string $title Title
     *
     * @return string
     */
    private function setFontFamiliy($font, $title)
    {
        return '<span style="font-family:' . $font . '">' . stripslashes($title) . '</span>';
    }

    /**
     * Set the background color for a piece of text.
     *
     * @param mixed $bg    Background color
     * @param mixed $title Title
     *
     * @return void
     */
    private function setBackgroundColor($bg, $title)
    {
        return '<span style="background-color:' . $bg . '">' . stripslashes($title) . '</span>';
    }

    /**
     * Set the font size fro a piece of text.
     *
     * @param mixed $size
     * @param mixed $text
     *
     * @return void
     */
    private function setFontSize($size, $text)
    {
        $title = stripslashes($text);
        return '<span style="font-size:' . $size . 'px">' . $title . '</span>';
    }
}
