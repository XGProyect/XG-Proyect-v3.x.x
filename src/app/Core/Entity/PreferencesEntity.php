<?php

declare(strict_types=1);

namespace App\Core\Entity;

use App\Core\Entity;

class PreferencesEntity extends Entity
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the preference id
     *
     * @return int
     */
    public function gePreferenceId(): int
    {
        return (int) $this->data['preference_id'];
    }

    /**
     * Return the preference user id
     *
     * @return int
     */
    public function getPreferenceUsedId(): int
    {
        return (int) $this->data['preference_user_id'];
    }

    /**
     * Return the preference nickname change
     *
     * @return int
     */
    public function getPreferenceNicknameChange(): int
    {
        return (int) $this->data['preference_nickname_change'];
    }

    /**
     * Return the preference spy probes
     *
     * @return int
     */
    public function getPreferenceSpyProbes(): int
    {
        return (int) $this->data['preference_spy_probes'];
    }

    /**
     * Return the preference planet sort
     *
     * @return int
     */
    public function getPreferencePlanetSort(): int
    {
        return (int) $this->data['preference_planet_sort'];
    }

    /**
     * Return the prefernce planet sort sequence
     *
     * @return int
     */
    public function getPreferencePlanetSortSequence(): int
    {
        return (int) $this->data['preference_planet_sort_sequence'];
    }

    /**
     * Return the preference vacation mode
     *
     * @return int
     */
    public function getPreferenceVacationMode(): int
    {
        return (int) $this->data['preference_vacation_mode'];
    }

    /**
     * Return the preference delete mode
     *
     * @return int
     */
    public function getPreferenceDeleteMode(): int
    {
        return (int) $this->data['preference_delete_mode'];
    }
}
