<?php declare (strict_types = 1);

/**
 * Preferences
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\game;

use App\core\entities\PreferencesEntity;

/**
 * Preferences Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Preferences
{
    /**
     *
     * @var array
     */
    private $preferences = [];

    /**
     *
     * @var int
     */
    private $current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $preferences
     * @param integer $current_user_id
     * @return void
     */
    public function __construct(array $preferences, int $current_user_id)
    {
        if (is_array($preferences)) {
            $this->setUp($preferences);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the preferences
     *
     * @return array
     */
    public function getPreferences(): array
    {
        $list_of_preferences = [];

        foreach ($this->preferences as $preference) {
            if (($preference instanceof PreferenceEntity)) {
                $list_of_preferences[] = $preference;
            }
        }

        return $list_of_preferences;
    }

    /**
     * Return current preference data
     *
     * @return \PreferencesEntity
     */
    public function getCurrentPreference(): PreferencesEntity
    {
        return $this->preferences[0];
    }

    /**
     * Check if is the preference owner
     *
     * @return boolean
     */
    public function isOwner(): bool
    {
        return ($this->getCurrentPreference()->getPreferenceOwner() === $this->getUserId());
    }

    /**
     * Check if the username change is allowed
     *
     * @return boolean
     */
    public function isNickNameChangeAllowed(): bool
    {
        return (($this->getCurrentPreference()->getPreferenceNicknameChange() + ONE_WEEK) < time());
    }

    /**
     * Check if the vacation mode is enabled
     *
     * @return boolean
     */
    public function isVacationModeOn(): bool
    {
        return ($this->getCurrentPreference()->getPreferenceVacationMode() > 0);
    }

    /**
     * Check if the vacation mode removal is allowed
     *
     * @return boolean
     */
    public function isVacationModeRemovalAllowed(): bool
    {
        return (($this->getCurrentPreference()->getPreferenceVacationMode() + ONE_DAY * 2) < time());
    }

    /**
     * Set up the list of preferences
     *
     * @param array $preferences Preferences
     *
     * @return void
     */
    private function setUp($preferences): void
    {
        foreach ($preferences as $preference) {
            $this->preferences[] = $this->createNewPreferencesEntity($preference);
        }
    }

    /**
     * Set the user id
     *
     * @param int $user_id User Id
     *
     * @return void
     */
    private function setUserId($user_id): void
    {
        $this->current_user_id = $user_id;
    }

    /**
     * Get the user id
     *
     * @return int
     */
    private function getUserId(): int
    {
        return $this->current_user_id;
    }

    /**
     * Create a new instance of PreferencesEntity
     *
     * @param array $preference Preference
     *
     * @return \PreferencesEntity
     */
    private function createNewPreferencesEntity($preference)
    {
        return new PreferencesEntity($preference);
    }
}
