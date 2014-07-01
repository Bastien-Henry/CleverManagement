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

    public function signup()
    {
        $form = new WalrusForm('form_signup');
        $this->register('formSignUp', $form->render());
        $this->setView('login');

        if (!empty($_POST)) {
            $res = $this->model('user')->signup();
            
            if (isset($res['errors'])) {
                $this->register('errors', $res['errors']);
            }
            else
            {
                $this->go('/CleverManagement/');
            }
        }
    }

    public function signin()
    {
        $form = new WalrusForm('form_signin');
        echo $form->render();

        if (!empty($_POST)) {
            if(!$this->model('user')->signin())
            {
             var_dump('if');
                $this->register('errors', array('credentials' => 'wrong email/password'));
            }
            else
            {
             var_dump('else');
             var_dump($_SESSION);
             die('hello');
                $this->go('/CleverManagement/');
            }
        }

        $this->setView('login');
    }
}