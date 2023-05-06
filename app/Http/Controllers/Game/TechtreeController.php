<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Libraries\Users;

class TechtreeController extends BaseController
{
    public const MODULE_ID = 10;

    /**
     *
     * @var \Objects
     */
    private $_resource;

    /**
     *
     * @var \Objects
     */
    private $_requirements;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/global', 'game/constructions', 'game/defenses', 'game/ships', 'game/technologies']);

        // requirements
        $this->_resource = $this->objects->getObjects();

        // requirements
        $this->_requirements = $this->objects->getRelations();
    }

    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        /**
         * Parse the items
         */
        $page = [
            'list_of_constructions' => $this->buildBlock('build'),
            'list_of_research' => $this->buildBlock('tech'),
            'list_of_ships' => $this->buildBlock('fleet'),
            'list_of_defenses' => $this->buildBlock('defenses'),
            'list_of_missiles' => $this->buildBlock('missiles'),
        ];

        // display the page
        $this->page->display(
            $this->template->set(
                'game/techtree_view',
                array_merge(
                    $this->langs->language,
                    $page
                )
            )
        );
    }

    /**
     * Build the block
     *
     * @param string $object_id
     *
     * @return array
     */
    private function buildBlock(string $object_id): array
    {
        $objects = $this->objects->getObjectsList($object_id);
        $list_of_objects = [];

        foreach ($objects as $object) {
            $list_of_objects[] = [
                'dpath' => DPATH,
                'tt_info' => $object,
                'tt_name' => $this->langs->language[$this->_resource[$object]],
                'tt_detail' => '',
                'requirements' => join('<br/>', $this->getRequirements($object)),
            ];
        }

        return $list_of_objects;
    }

    /**
     * Build the requirements list
     *
     * @param int $object
     *
     * @return array
     */
    private function getRequirements(int $object, int $current_level = 0): array
    {
        $list_of_requirements = [];

        if (!isset($this->_requirements[$object])) {
            return $list_of_requirements;
        }

        foreach ($this->_requirements[$object] as $requirement => $required_level) {
            $color = 'Red';
            $current_resource_level = $this->planet[$this->_resource[$requirement]] ?? $this->user[$this->_resource[$requirement]] ?? 0;
            $current_resource_level = max($current_resource_level, $current_level);

            if ($current_resource_level >= $required_level) {
                $color = 'Green';
                $display_level = $required_level;
            } else {
                $display_level = $current_resource_level . '/' . $required_level;
            }

            $list_of_requirements[] = FormatLib::{'color' . $color}(
                FormatLib::formatLevel($this->langs->language[$this->_resource[$requirement]], $this->langs->line('level'), $display_level)
            );
        }

        return $list_of_requirements;
    }
}
