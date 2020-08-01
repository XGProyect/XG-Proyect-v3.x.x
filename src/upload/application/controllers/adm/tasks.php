<?php

declare (strict_types = 1);

/**
 * Tasks Controller
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
namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib;
use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\TimingLibrary as Timing;

/**
 * Tasks Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Tasks extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/tasks']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::authorization($this->user['user_authlevel'], 'observation') != 1) {
            die(AdministrationLib::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/tasks_view',
                array_merge(
                    $this->langs->language,
                    $this->buildUpdatesBlock()
                )
            )
        );
    }

    /**
     * Build the tasks list
     *
     * @return array
     */
    private function buildUpdatesBlock(): array
    {
        $update_tasks = ['stat_last_update', 'last_backup', 'last_cleanup'];
        $update_blocks = [];

        foreach ($update_tasks as $task) {
            $update_blocks[] = $this->getTaskData($task);
        }

        return ['tasks_list' => $update_blocks];
    }

    /**
     * Get the task information
     *
     * @return array
     */
    private function getTaskData(string $task): array
    {
        $next_run = '-';
        $last_run = '-';

        if ($this->isTaskScheduled($task)) {
            $task_time = Functions::readConfig($task);
            $next_run = Timing::formatExtendedDate($task_time);
            $last_run = Format::prettyTime(time() - $task_time);
        }

        return [
            'name' => $this->langs->line('ta_' . $task),
            'next_run' => $next_run,
            'last_run' => $last_run,
            'actions' => $this->{'get' . ucwords(strtr($task, ['_' => ''])) . 'Actions'}(),
        ];
    }

    /**
     * Check if the task is scheduled, return true if it is
     *
     * @param string $task
     * @return boolean
     */
    private function isTaskScheduled(string $task): bool
    {
        return !($task == 'last_backup' && Functions::readConfig('auto_backup') == 0);
    }

    /**
     * Get actions for the points update task
     *
     * @return string
     */
    private function getStatLastUpdateActions(): string
    {
        return Functions::setUrl(
            'admin.php?page=rebuildhighscores',
            $this->langs->line('ta_buildstats_title'),
            '<i class="fas fa-play" data-toggle="popover" data-placement="top"
            data-trigger="hover" data-content="' . $this->langs->line('ta_buildstats_title') . '"></i>'
        );
    }

    /**
     * Get actions for the backup task
     *
     * @return string
     */
    private function getLastBackupActions(): string
    {
        return Functions::setUrl(
            'admin.php?page=backup',
            $this->langs->line('ta_backup_title'),
            '<i class="fas fa-cogs" data-toggle="popover" data-placement="top"
            data-trigger="hover" data-content="' . $this->langs->line('ta_backup_title') . '"></i>'
        );
    }

    /**
     * Get actions for clean up task
     *
     * @return string
     */
    private function getLastCleanupActions(): string
    {
        return '';
    }
}

/* end of tasks.php */
