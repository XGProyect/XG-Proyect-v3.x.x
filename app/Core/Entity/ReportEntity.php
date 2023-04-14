<?php

namespace App\Core\Entity;

use App\Core\Entity;

class ReportEntity extends Entity
{
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
        return $this->data['report_owners'];
    }

    /**
     * Return the report rid
     *
     * @return string
     */
    public function getReportId()
    {
        return $this->data['report_rid'];
    }

    /**
     * Return the report content
     *
     * @return string
     */
    public function getReportContent()
    {
        return $this->data['report_content'];
    }

    /**
     * Return the report destroyed
     *
     * @return string
     */
    public function getReportDestroyed()
    {
        return $this->data['report_destroyed'];
    }

    /**
     * Return the report time
     *
     * @return string
     */
    public function getReportTime()
    {
        return $this->data['report_time'];
    }
}
