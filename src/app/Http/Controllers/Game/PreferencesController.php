<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Core\Enumerators\PreferencesEnumerator as PrefEnum;
use App\Libraries\FormatLib as Format;
use App\Libraries\Functions;
use App\Libraries\Game\Preferences as Pref;
use App\Libraries\TimingLibrary as Timing;
use App\Libraries\Users;
use App\Models\Game\Preferences;

class PreferencesController extends BaseController
{
    public const MODULE_ID = 21;

    private ?Pref $preferences = null;
    private array $fields_to_update = [];
    private string $error = '';
    private bool $post = false;
    private Preferences $preferencesModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/preferences']);

        $this->preferencesModel = new Preferences();

        // init a new preferences object
        $this->setUpPreferences();
    }

    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new preferences object that will handle all the preferences
     * creation methods and actions
     *
     * @return void
     */
    private function setUpPreferences(): void
    {
        $this->preferences = new Pref(
            $this->preferencesModel->getAllPreferencesByUserId((int) $this->user['user_id']),
            (int) $this->user['user_id']
        );
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $vacation_mode = filter_input(INPUT_POST, 'preference_vacation_mode');

        if ($vacation_mode) {
            $this->post = true;

            $this->validateVacationMode();
        }

        $preferences = filter_input_array(INPUT_POST, [
            'new_user_name' => FILTER_UNSAFE_RAW,
            'confirmation_user_password' => FILTER_UNSAFE_RAW,
            'current_user_password' => FILTER_UNSAFE_RAW,
            'new_user_password' => FILTER_UNSAFE_RAW,
            'new_user_email' => FILTER_VALIDATE_EMAIL,
            'confirmation_email_password' => FILTER_UNSAFE_RAW,
            'preference_spy_probes' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 1, 'min_range' => 1, 'max_range' => 99],
            ],
            'preference_planet_sort' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 0, 'min_range' => 0, 'max_range' => (count(PrefEnum::order) - 1)],
            ],
            'preference_planet_sort_sequence' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 0, 'min_range' => 0, 'max_range' => (count(PrefEnum::sequence) - 1)],
            ],
            'preference_delete_mode' => FILTER_UNSAFE_RAW,
        ]);

        if ($preferences) {
            $this->post = true;

            $this->validateDeleteMode($preferences);

            // remove values that din't pass the validation
            $preferences = array_diff($preferences, [null, false]);

            // run validations
            $this->validateNewUserName($preferences);
            $this->validateNewPassword($preferences);
            $this->validateNewEmail($preferences);
            $this->validateSpyProbes($preferences);
            $this->validatePlanetSort($preferences);
            $this->validatePlanetSortSequence($preferences);

            if ($this->error == '') {
                $this->preferencesModel->updateValidatedFields(
                    $this->fields_to_update,
                    (int) $this->user['user_id']
                );

                $this->setUpPreferences();
            }
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'game/preferences_view',
                array_merge(
                    $this->langs->language,
                    $this->setMessageDisplay(),
                    $this->setUserData(),
                    [
                        'preference_spy_probes' => $this->preferences->getCurrentPreference()->getPreferenceSpyProbes(),
                        'sort_planet' => $this->sortPlanetOptions(),
                        'sort_sequence' => $this->sortSequenceOptions(),
                    ],
                    $this->setVacationMode(),
                    $this->setDeleteMode()
                )
            )
        );
    }

    /**
     * Display the message block
     *
     * @return array
     */
    private function setMessageDisplay(): array
    {
        $message = [
            'status_message' => [],
        ];

        if ($this->post) {
            $message = [
                'status_message' => '',
                '/status_message' => '',
                'error_color' => ($this->error == '' ? '#00FF00' : '#FF0000'),
                'error_text' => ($this->error == '' ? $this->langs->line('pr_ok_settings_saved') : $this->error),
            ];
        }

        return $message;
    }

    /**
     * Set the user data for the view
     *
     * @return array
     */
    private function setUserData(): array
    {
        return [
            'user_name' => $this->user['user_name'],
            'hide_nickname_change' => ($this->preferences->isNickNameChangeAllowed() ? '' : 'style="display: none"'),
            'user_email' => $this->user['user_email'],
        ];
    }

    /**
     * Returns an array with the different options to sort a planet
     *
     * @return array
     */
    private function sortPlanetOptions(): array
    {
        $order_options = [];

        foreach (PrefEnum::order as $order => $value) {
            $order_options[] = [
                'value' => $value,
                'selected' => (
                    $value == $this->preferences->getCurrentPreference()->getPreferencePlanetSort() ? 'selected="selected"' : ''
                ),
                'text' => $this->langs->line('pr_order_' . $order),
            ];
        }

        return $order_options;
    }

    /**
     * Returns an array with the different sequence options to sort a planet
     *
     * @return array
     */
    private function sortSequenceOptions(): array
    {
        $sequence_options = [];

        foreach (PrefEnum::sequence as $sequence => $value) {
            $sequence_options[] = [
                'value' => $value,
                'selected' => (
                    $value == $this->preferences->getCurrentPreference()->getPreferencePlanetSortSequence() ? 'selected="selected"' : ''
                ),
                'text' => $this->langs->line('pr_sorting_sequence_' . $sequence),
            ];
        }

        return $sequence_options;
    }

    /**
     * Set the vacation mode data for the view
     *
     * @return array
     */
    private function setVacationMode(): array
    {
        if ($this->preferences->isVacationModeOn()) {
            return [
                'hide_vacation_invalid' => 'style="display: none"',
                'pr_vacation_mode_active' => Format::strongText(
                    Format::colorRed($this->langs->line('pr_vacation_mode_active'))
                ),
                'disabled' => ($this->preferences->isVacationModeRemovalAllowed() ? '' : 'style="display: none"'),
            ];
        }

        if ($this->preferencesModel->isEmpireActive((int) $this->user['user_id'])) {
            return [
                'disabled' => 'style="display: none"',
                'pr_vacation_mode_active' => Format::strongText(
                    Format::colorRed($this->langs->line('pr_empire_active') . $this->langs->line('pr_empire_active_fleet'))
                ),
            ];
        }

        return [
            'hide_no_vacation' => 'style="display: none"',
            'pr_vacation_mode_active' => '',
        ];
    }

    /**
     * Set the delete mode data for the view
     *
     * @return array
     */
    private function setDeleteMode(): array
    {
        if ($this->preferences->getCurrentPreference()->getPreferenceDeleteMode() > 0) {
            return [
                'pr_delete_account' => Format::colorRed(strtr(
                    $this->langs->line('pr_delete_mode_active'),
                    [
                        '%s' => Timing::formatExtendedDate(
                            $this->preferences->getCurrentPreference()->getPreferenceDeleteMode() + ONE_WEEK
                        ),
                    ]
                )),
                'preference_delete_mode' => 'checked="checked"',
                'hide_delete' => 'style="display: none"',
            ];
        }

        return [];
    }

    /**
     * Validate new user name
     *
     * @param array $preferences
     * @return void
     */
    private function validateNewUserName(array $preferences): void
    {
        if (isset($preferences['new_user_name'])
            && isset($preferences['confirmation_user_password'])
            && $this->preferences->isNickNameChangeAllowed()) {
            if (password_verify($preferences['confirmation_user_password'], $this->user['user_password'])) {
                $user_name_len = strlen(trim($preferences['new_user_name']));

                if ($user_name_len > 3 && $user_name_len <= 20) {
                    if (!$this->preferencesModel->checkIfNicknameExists($preferences['new_user_name'])) {
                        $this->fields_to_update['user_name'] = $preferences['new_user_name'];
                        $this->fields_to_update['preference_nickname_change'] = time();
                    } else {
                        $this->error = $this->langs->line('pr_error_nick_in_use');
                    }
                } else {
                    $this->error = strtr(
                        $this->langs->line('pr_error_user_invalid_characters'),
                        ['%s' => $preferences['new_user_name']]
                    );
                }
            } else {
                $this->error = $this->langs->line('pr_error_wrong_password');
            }
        }
    }

    /**
     * Validate new password
     *
     * @param array $preferences
     * @return void
     */
    private function validateNewPassword(array $preferences): void
    {
        if (isset($preferences['current_user_password'])
            && isset($preferences['new_user_password'])) {
            if (password_verify($preferences['current_user_password'], $this->user['user_password'])) {
                $this->fields_to_update['user_password'] = Functions::hash(trim($preferences['new_user_password']));
            } else {
                $this->error = $this->langs->line('pr_error_wrong_password');
            }
        }
    }

    /**
     * Validate new email
     *
     * @param array $preferences
     * @return void
     */
    private function validateNewEmail(array $preferences): void
    {
        if (isset($preferences['new_user_email'])
            && isset($preferences['confirmation_email_password'])) {
            if (password_verify($preferences['confirmation_email_password'], $this->user['user_password'])) {
                $user_email_len = strlen(trim($preferences['new_user_email']));

                if ($user_email_len > 4 && $user_email_len <= 64) {
                    if (!$this->preferencesModel->checkIfEmailExists($preferences['new_user_email'])) {
                        $this->fields_to_update['user_email'] = $preferences['new_user_email'];
                    } else {
                        $this->error = $this->langs->line('pr_error_email_in_use');
                    }
                } else {
                    $this->error = strtr(
                        $this->langs->line('pr_error_email_invalid_characters'),
                        ['%s' => $preferences['new_user_email']]
                    );
                }
            } else {
                $this->error = $this->langs->line('pr_error_wrong_password');
            }
        }
    }

    /**
     * Validate spy probes
     *
     * @param array $preferences
     * @return void
     */
    private function validateSpyProbes(array $preferences): void
    {
        $this->fields_to_update['preference_spy_probes'] = $preferences['preference_spy_probes'];
    }

    /**
     * Validate planet sort
     *
     * @param array $preferences
     * @return void
     */
    private function validatePlanetSort(array $preferences): void
    {
        $this->fields_to_update['preference_planet_sort'] = $preferences['preference_planet_sort'];
    }

    /**
     * Validate planet sort sequence
     *
     * @param array $preferences
     * @return void
     */
    private function validatePlanetSortSequence(array $preferences): void
    {
        $this->fields_to_update['preference_planet_sort_sequence'] = $preferences['preference_planet_sort_sequence'];
    }

    /**
     * Validate vacation mode
     *
     * @return void
     */
    private function validateVacationMode(): void
    {
        if ($this->preferences->isVacationModeOn()) {
            if ($this->preferences->isVacationModeRemovalAllowed()) {
                $this->preferencesModel->endVacation((int) $this->user['user_id']);
            }
        } else {
            $this->preferencesModel->startVacation((int) $this->user['user_id']);
        }
    }

    /**
     * Validate delete mode
     *
     * @param array $preferences
     * @return void
     */
    private function validateDeleteMode(array $preferences): void
    {
        if (isset($preferences['preference_delete_mode'])
            && $preferences['preference_delete_mode'] = 'on') {
            $this->fields_to_update['preference_delete_mode'] = time();
        } else {
            $this->fields_to_update['preference_delete_mode'] = null;
        }
    }
}
