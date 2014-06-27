<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
 * Class HelloController
 * @package engine\controllers
 */
class HelloController extends WalrusController
{

    public function run()
    {
        $this->register('username', 'lel');
        $this->setView('world');
        var_dump($_SESSION);
    }
}
