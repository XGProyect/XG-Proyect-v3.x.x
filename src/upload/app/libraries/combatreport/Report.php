<?php
/**
 * Report
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\combatreport;

use App\core\entities\ReportEntity;
use App\libraries\enumerators\ReportStatusEnumerator as ReportStatus;

/**
 * Report Class
 *
 * @category Classes
 * @package  combatreport
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Report
{
    /**
     *
     * @var array
     */
    private $_reports = [];

    /**
     *
     * @var int
     */
    private $_current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $reports         Reports
     * @param int   $current_user_id Current User ID
     *
     * @return void
     */
    public function __construct($reports, $current_user_id)
    {
        if (is_array($reports)) {
            $this->setUp($reports);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the reports provided by the query result
     *
     * @return array
     */
    public function getAllReports()
    {
        $list_of_reports = [];

        foreach ($this->_reports as $report) {
            if ($report instanceof ReportEntity) {
                $list_of_reports[] = $report;
            }
        }

        return $list_of_reports;
    }

    /**
     * Get all the reports provided by the query result, filtered by current user
     *
     * @return array
     */
    public function getAllReportsOwnedByUserId()
    {
        $list_of_reports = [];

        foreach ($this->_reports as $report) {
            if (($report instanceof ReportEntity) && $this->isOwnRequest($report)) {
                $list_of_reports[] = $report;
            }
        }

        return $list_of_reports;
    }

    /**
     * Get all the reports provided by the query result, that are destroyed
     *
     * @return array
     */
    public function getAllDestroyedReports()
    {
        $list_of_reports = [];

        foreach ($this->_reports as $report) {
            if (($report instanceof ReportEntity) && $this->isDestroyedReport($report)) {
                $list_of_reports[] = $report;
            }
        }

        return $list_of_reports;
    }

    /**
     * Get first report owners as an array
     *
     * @return array
     */
    public function getFirstReportOwnersAsArray(): array
    {
        $owners = [];

        foreach ($this->_reports as $report) {
            if (($report instanceof ReportEntity)) {
                $owners[] = $this->getReportOwnersAsArray($report);
                break;
            }
        }

        return $owners[0] ?? $owners;
    }

    /**
     *
     * @param type $report_id
     *
     * @return ReportEntity
     */
    public function getReportOwnersAsArrayByReportId($report_id)
    {
        $owners = [];

        foreach ($this->_reports as $report) {
            if (($report instanceof ReportEntity) && ($report->getReportId() == $report_id)) {
                $owners[] = $this->getReportOwnersAsArray($report);
                break;
            }
        }

        return $owners;
    }

    /**
     * Get report owners as an array
     *
     * @param ReportEntity $report
     *
     * @return array
     */
    private function getReportOwnersAsArray(ReportEntity $report)
    {
        return explode(',', $report->getReportOwners());
    }

    /**
     * Check if a report is destroyed
     *
     * @param ReportEntity $report Report
     *
     * @return boolean
     */
    private function isDestroyedReport(ReportEntity $report)
    {
        return ($report->getReportDestroyed() == ReportStatus::fleetDestroyed);
    }

    /**
     * Check if is the report owner
     *
     * @param ReportEntity $report Report
     *
     * @return boolean
     */
    private function isOwnRequest(ReportEntity $report)
    {
        return (in_array($this->getUserId(), $this->getReportOwnersAsArray($report)));
    }

    /**
     * Set up the list of reports
     *
     * @param array $reports Reports
     *
     * @return void
     */
    private function setUp($reports)
    {
        foreach ($reports as $report) {
            if (is_array($report)) {
                $this->_reports[] = $this->createNewReportEntity($report);
            }
        }
    }

    /**
     *
     * @param int $user_id User Id
     */
    private function setUserId($user_id)
    {
        $this->_current_user_id = $user_id;
    }

    /**
     *
     * @return int
     */
    private function getUserId()
    {
        return $this->_current_user_id;
    }

    /**
     * Create a new instance of ReportEntity
     *
     * @param array $report Report
     *
     * @return \ReportEntity
     */
    private function createNewReportEntity($report)
    {
        return new ReportEntity($report);
    }
}
