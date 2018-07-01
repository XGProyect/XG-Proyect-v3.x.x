<?php
/**
 * Report entity
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

use Exception;

/**
 * Report Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ReportEntity
{

    /**
     *
     * @var array
     */
    private $_report = [];

    /**
     * Init with the report data
     * 
     * @param array $report Report
     */
    public function __construct($report)
    {
        $this->setReport($report);
    }

    /**
     * Set the current report
     * 
     * @param array $report Report
     * 
     * @throws Exception
     * 
     * @retun void
     */
    private function setReport($report)
    {
        try {

            if (!is_array($report)) {
                
                return null;
            }
            
            $this->_report = $report;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Return the report owners
     * 
     * @return string
     */
    public function getReportOwners()
    {
        return $this->_report['report_owners'];
    }

    /**
     * Return the report rid
     * 
     * @return string
     */
    public function getReportId()
    {
        return $this->_report['report_rid'];
    }

    /**
     * Return the report content
     * 
     * @return string
     */
    public function getReportContent()
    {
        return $this->_report['report_content'];
    }

    /**
     * Return the report destroyed
     * 
     * @return string
     */
    public function getReportDestroyed()
    {
        return $this->_report['report_destroyed'];
    }

    /**
     * Return the report time
     * 
     * @return string
     */
    public function getReportTime()
    {
        return $this->_report['report_time'];
    }
}

/* end of ReportEntity.php */
