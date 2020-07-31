<?php

declare (strict_types = 1);

/**
 * Server Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\adm;

use application\core\Database;
use application\libraries\FunctionsLib as Functions;

/**
 * Server Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Server
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Read all server configurations
     *
     * @return array
     */
    public function readAllConfigs(): array
    {
        return Functions::readConfig('', true);
    }

    /**
     * Read an specific config variable
     *
     * @param string $config_name
     * @return string
     */
    public function readConfig(string $config_name): string
    {
        return Functions::readConfig($config_name);
    }

    /**
     * Update all configs, we asume they were validated
     *
     * @param array $configs
     * @return void
     */
    public function updateConfigs(array $configs): void
    {
        foreach ($configs as $config_name => $config_value) {
            Functions::updateConfig($config_name, $config_value);
        }
    }
}

/* end of announcement.php */