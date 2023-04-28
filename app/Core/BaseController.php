<?php

declare(strict_types=1);

namespace App\Core;

use App\Libraries\Page;
use App\Libraries\Users;
use CiLang;

abstract class BaseController
{
    protected ?Users $userLibrary = null;
    protected ?array $user = [];
    protected ?array $planet = [];
    protected Objects $objects;
    protected ?Page $page = null;
    protected ?Template $template = null;
    protected CiLang $langs;

    public function __construct()
    {
        $this->userLibrary = new Users();
        $this->user = $this->userLibrary->getUserData();
        $this->planet = $this->userLibrary->getPlanetData();

        $this->objects = new Objects();
        $this->page = new Page($this->userLibrary);
        $this->template = new Template();
    }

    /**
     * @param string|array $languageFile
     */
    protected function loadLang($languageFile): void
    {
        $this->langs = (new Language())->loadLang($languageFile, true);
    }
}
