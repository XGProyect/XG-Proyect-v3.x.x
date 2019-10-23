<?php
/**
 * Report entity
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

use application\core\Entity;

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
class ReportEntity extends Entity
{

    /**
     * Constructor
     * 
     * @param array $data Data
     * 
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the report owners
     * 
     * @return string
     */
    public function getReportOwners()
    {
        return $this->_data['report_owners'];
    }

    /**
     * Return the report rid
     * 
     * @return string
     */
    public function getReportId()
    {
        return $this->_data['report_rid'];
    }

    /**
     * Return the report content
     * 
     * @return string
     */
    public function getReportContent()
    {
        return $this->_data['report_content'];
    }

    /**
     * Return the report destroyed
     * 
     * @return string
     */
    public function getReportDestroyed()
    {
        return $this->_data['report_destroyed'];
    }

    /**
     * Return the report time
     * 
     * @return string
     */
    public function getReportTime()
    {
        return $this->_data['report_time'];
    }
}

/* end of ReportEntity.php */
