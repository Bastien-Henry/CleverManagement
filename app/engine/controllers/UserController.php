<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
 * Class UserController
 * @package engine\controllers
 */
class UserController extends WalrusController
{

    public function run()
    {
        $this->register('speak', 'Hello World!');
        $this->setView('user');
    }
}
