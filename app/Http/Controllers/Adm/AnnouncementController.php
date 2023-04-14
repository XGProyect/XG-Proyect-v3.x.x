<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Core\Enumerators\MessagesEnumerator;
use App\Core\Enumerators\UserRanksEnumerator as UserRanks;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\FormatLib as Format;
use App\Libraries\Functions;
use App\Models\Adm\Announcement;

class AnnouncementController extends BaseController
{
    private array $alerts = [];
    private Announcement $announcementModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/announcement']);

        $this->announcementModel = new Announcement();
    }

    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $action = filter_input_array(
            INPUT_POST,
            [
                'subject' => FILTER_UNSAFE_RAW,
                'color-picker' => [
                    'filter' => FILTER_CALLBACK,
                    'options' => [$this, 'isValidColor'],
                ],
                'message' => FILTER_UNSAFE_RAW,
                'mail' => FILTER_UNSAFE_RAW,
                'text' => [
                    'filter' => FILTER_UNSAFE_RAW,
                    'options' => ['min_range' => 1, 'max_range' => 5000],
                ],
            ],
            false
        );

        if ($action) {
            if (isset($action['text']) && $action['text'] != '') {
                if (isset($action['message'])) {
                    $this->doMessageAction($action);
                }

                if (isset($action['mail'])) {
                    $this->doEmailAction($action);
                }
            } else {
                $this->alerts[] = Administration::saveMessage('warning', $this->langs->line('an_not_sent'));
            }
        }
    }

    /**
     * Send the annoucement as a private message to every user
     *
     * @param array $post
     * @return void
     */
    private function doMessageAction(array $post): void
    {
        $players = $this->announcementModel->getAllPlayers();

        if (isset($post['color-picker'])) {
            $color = $post['color-picker'];
        } else {
            $color = $this->getMessageColor()[$this->user['user_authlevel']];
        }

        $level = $this->langs->language['user_level'][$this->user['user_authlevel']];
        $time = time();

        $from = Format::customColor($level, $color);
        $subject = Format::customColor(($post['subject'] ?? $this->langs->line('an_none')), $color);
        $message = Format::customColor($post['text'], $color);

        foreach ($players as $player) {
            Functions::sendMessage(
                $player['user_id'],
                $this->user['user_id'],
                $time,
                MessagesEnumerator::GENERAL,
                $from,
                $subject,
                strtr($message, ['%player%' => Format::strongText($player['user_name'])]),
                true
            );
        }

        $this->alerts[] = Administration::saveMessage('ok', $this->langs->line('an_sent'));
    }

    /**
     * Send the annoucement as an email to every user
     *
     * @param array $post
     * @return void
     */
    private function doEmailAction(array $post): void
    {
        $players = $this->announcementModel->getAllPlayers();
        $from = [
            'mail' => Functions::readConfig('admin_email'),
            'name' => Functions::readConfig('game_name'),
        ];
        $sent_count = 0;
        $results = [];

        foreach ($players as $player) {
            $result = Functions::sendEmail(
                $player['user_email'],
                ($post['subject'] ?? $this->langs->line('an_none')),
                strtr($post['text'], ['%player%' => Format::strongText($player['user_name'])]),
                $from
            );

            $results[] = $player['user_name'] . ': ' . ($result ? $this->langs->line('an_email_sent') : $this->langs->line('an_email_failed'));

            // 20 per row
            if ($sent_count % 20 == 0) {
                sleep(1); // wait, prevent flooding
            }

            $sent_count++;
        }

        $this->alerts[] = Administration::saveMessage(
            'info',
            strtr(
                $this->langs->line('an_delivery_result'),
                ['%s' => join('<br>', $results)]
            )
        );
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
                'adm/announcement_view',
                array_merge(
                    $this->langs->language,
                    $this->buildColorPicker(),
                    [
                        'js_path' => JS_PATH,
                        'alert' => $this->alerts ? join('', $this->alerts) : '',
                    ]
                )
            )
        );
    }

    /**
     * Build a list of colors
     *
     * @return array
     */
    private function buildColorPicker(): array
    {
        $colors_list = [];

        foreach (Format::getHTMLColorsNameList() as $color) {
            $colors_list[] = [
                'color' => $color,
            ];
        }

        return [
            'colors' => $colors_list,
        ];
    }

    /**
     * Check whether if it's a valid color, returns an empty string if it's not
     *
     * @param string $color
     * @return string
     */
    private function isValidColor(string $color): string
    {
        if (in_array($color, Format::getHTMLColorsNameList())) {
            return $color;
        }

        return '';
    }

    /**
     * Get the color based on the rank
     *
     * @return array
     */
    private function getMessageColor(): array
    {
        return [
            UserRanks::GO => 'yellow',
            UserRanks::SGO => 'skyblue',
            UserRanks::ADMIN => 'red',
        ];
    }
}
