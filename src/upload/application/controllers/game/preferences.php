<?php

declare(strict_types=1);

/**
 * Preferences Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\core\entities\PreferencesEntity;
use application\core\enumerators\PreferencesEnumerator as PrefEnum;
use application\libraries\FunctionsLib;
use application\libraries\game\Preferences as Pref;
use const MODULE_ID;

/**
 * Preferences Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Preferences extends Controller
{

    const MODULE_ID = 21;
    
    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     * Reference to Preferences library
     * 
     * @var \Preferences
     */
    private $_preferences = null;

    /**
     * List of fields to update
     *
     * @var array
     */
    private $_fields_to_update = [];

    /**
     * Contains an error message
     *
     * @var string
     */
    private $_error = '';

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/preferences');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // time to do something
        $this->runAction();

        // init a new buddy object
        $this->setUpPreferences();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new preferences object that will handle all the preferences
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpPreferences()
    {
        $this->_preferences = new Pref(
            $this->Preferences_Model->getAllPreferencesByUserId((int)$this->_user['user_id']),
            (int)$this->_user['user_id']
        );
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        $preferences = filter_input_array(INPUT_POST, [
            'new_user_name' => FILTER_SANITIZE_STRING,
            'confirmation_user_password' => FILTER_SANITIZE_STRING,
            'current_user_password' => FILTER_SANITIZE_STRING,
            'new_user_password' => FILTER_SANITIZE_STRING,
            'new_user_email' => FILTER_VALIDATE_EMAIL,
            'confirmation_email_password' => FILTER_SANITIZE_STRING,
            'preference_spy_probes' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 1, 'min_range' => 1, 'max_range' => 99]
            ],
            'preference_planet_sort' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 0, 'min_range' => 0, 'max_range' => (count(PrefEnum::order)-1)]
            ],
            'preference_planet_sort_sequence' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['default' => 0, 'min_range' => 0, 'max_range' => (count(PrefEnum::sequence)-1)]
            ],
            'preference_delete_mode' => FILTER_SANITIZE_STRING
        ]);

        if ($preferences) {

            // remove values that din't pass the validation
            $preferences = array_diff($preferences, [null, false]);

            // run validations
            $this->validateNewUserName($preferences);
            $this->validateNewPassword($preferences);
            $this->validateNewEmail($preferences);
            $this->validateSpyProbes($preferences);
            $this->validatePlanetSort($preferences);
            $this->validatePlanetSortSequence($preferences);
            $this->validateDeleteMode($preferences);

            if ($this->_error == '') {

                $this->Preferences_Model->updateValidatedFields(
                    $this->_fields_to_update, (int)$this->_user['user_id']
                );
            }
        }
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        parent::$page->display(
            $this->getTemplate()->set(
                'game/preferences_view',
                array_merge(
                    $this->getLang(),
                    [
                        'user_name' => $this->_user['user_name'],
                        'user_email' => $this->_user['user_email'],
                        'preference_spy_probes' => $this->_preferences->getCurrentPreference()->getPreferenceSpyProbes(),
                        'sort_planet' => $this->sortPlanetOptions(),
                        'sort_sequence' => $this->sortSequenceOptions()
                    ]
                )
            )
        );
    }

    /**
     * Returns an array with the different options to sort a planet
     *
     * @return array
     */
    private function sortPlanetOptions(): array
    {
        $order_options = [];

        foreach(PrefEnum::order as $order => $value) {

            $order_options[] = [
                'value' => $value,
                'selected' => ($value == $this->_preferences->getCurrentPreference()->getPreferencePlanetSort() ? 'selected="selected"' : ''),
                'text' => $this->getLang()['pr_order_' . $order]
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

        foreach(PrefEnum::sequence as $sequence => $value) {

            $sequence_options[] = [
                'value' => $value,
                'selected' => ($value == $this->_preferences->getCurrentPreference()->getPreferencePlanetSortSequence() ? 'selected="selected"' : ''),
                'text' => $this->getLang()['pr_sorting_sequence_' . $sequence]
            ];
        }

        return $sequence_options;
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
            && isset($preferences['confirmation_user_password'])) {

            if (sha1($preferences['confirmation_user_password']) == $this->_user['user_password']) {

                $user_name_len = strlen(trim($preferences['new_user_name']));

                if ($user_name_len > 3 && $user_name_len <= 20) {

                    if (!$this->Preferences_Model->checkIfNicknameExists($preferences['new_user_name'])) {

                        $this->_fields_to_update['user_name'] = $preferences['new_user_name'];
                    } else {

                        $this->_error = $this->getLang()['pr_error_nick_in_use'];
                    }
                } else {

                    $this->_error = $this->getLang()['pr_error_user_invalid_characters'];
                }
            } else {

                $this->_error = $this->getLang()['pr_error_wrong_password'];
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

            if (sha1($preferences['current_user_password']) == $this->_user['user_password']) {

                $this->_fields_to_update['user_password'] = sha1(trim($preferences['new_user_password']));
            } else {

                $this->_error = $this->getLang()['pr_error_wrong_password'];
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

            if (sha1($preferences['confirmation_email_password']) == $this->_user['user_password']) {

                $user_email_len = strlen(trim($preferences['new_user_email']));

                if ($user_email_len > 4 && $user_email_len <= 64) {

                    if (!$this->Preferences_Model->checkIfEmailExists($preferences['new_user_email'])) {

                        $this->_fields_to_update['user_email'] = $preferences['new_user_email'];
                    } else {

                        $this->_error = $this->getLang()['pr_error_email_in_use'];
                    }
                } else {

                    $this->_error = $this->getLang()['pr_error_email_invalid_characters'];
                }
            } else {

                $this->_error = $this->getLang()['pr_error_wrong_password'];
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
        $this->_fields_to_update['preference_spy_probes'] = $preferences['preference_spy_probes'];
    }

    /**
     * Validate planet sort
     *
     * @param array $preferences
     * @return void
     */
    private function validatePlanetSort(array $preferences): void
    {
        $this->_fields_to_update['preference_planet_sort'] = $preferences['preference_planet_sort'];
    }

    /**
     * Validate planet sort sequence
     *
     * @param array $preferences
     * @return void
     */
    private function validatePlanetSortSequence(array $preferences): void
    {
        $this->_fields_to_update['preference_planet_sort_sequence'] = $preferences['preference_planet_sort_sequence'];
    }

    private function validateVacationMode(array $preferences): void
    {

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
            
            $this->_fields_to_update['preference_delete_mode'] = time();
        }
    }
}

/* end of preferences.php */
