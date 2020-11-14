<?php
/**
 * Alliance entity
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core\entities;

use App\core\Entity;

/**
 * AllianceEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class AllianceEntity extends Entity
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
     * Return the alliance id
     *
     * @return string
     */
    public function getAllianceId()
    {
        return $this->data['alliance_id'];
    }

    /**
     * Return the alliance name
     *
     * @return string
     */
    public function getAllianceName()
    {
        return $this->data['alliance_name'];
    }

    /**
     * Return the alliance tag
     *
     * @return string
     */
    public function getAllianceTag()
    {
        return $this->data['alliance_tag'];
    }

    /**
     * Return the alliance owner
     *
     * @return string
     */
    public function getAllianceOwner()
    {
        return $this->data['alliance_owner'];
    }

    /**
     * Return the alliance register time
     *
     * @return string
     */
    public function getAllianceRegisterTime()
    {
        return $this->data['alliance_register_time'];
    }

    /**
     * Return the alliance description
     *
     * @return string
     */
    public function getAllianceDescription()
    {
        return $this->data['alliance_description'];
    }

    /**
     * Return the alliance web
     *
     * @return string
     */
    public function getAllianceWeb()
    {
        return $this->data['alliance_web'];
    }

    /**
     * Return the alliance text
     *
     * @return string
     */
    public function getAllianceText()
    {
        return $this->data['alliance_text'];
    }

    /**
     * Return the alliance image
     *
     * @return string
     */
    public function getAllianceImage()
    {
        return $this->data['alliance_image'];
    }

    /**
     * Return the alliance request
     *
     * @return string
     */
    public function getAllianceRequest()
    {
        return $this->data['alliance_request'];
    }

    /**
     * Return the alliance request not allow
     *
     * @return string
     */
    public function getAllianceRequestNotAllow()
    {
        return $this->data['alliance_request_notallow'];
    }

    /**
     * Return the alliance ranks
     *
     * @return string
     */
    public function getAllianceRanks()
    {
        return $this->data['alliance_ranks'];
    }

    /**
     * Return the alliance members
     *
     * @return string
     */
    public function getAllianceMembers()
    {
        return $this->data['alliance_members'];
    }
}
