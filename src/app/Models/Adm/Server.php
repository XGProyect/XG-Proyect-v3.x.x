<?php

declare(strict_types=1);

namespace App\Models\Adm;

use App\Core\Model;
use App\Libraries\Functions;

class Server extends Model
{
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
