<?php

namespace app\engine\controllers;
 
use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
* Class ProjectController
* @package engine\controllers
*/
class SessionController extends WalrusController
{
    public function create($id_project, $id_step, $id_task)
    {
        $form = new WalrusForm('form_session_create');
        if(!empty($_POST))
        {
            $this->model('session')->create($id_task);
            $this->go('/CleverManagement');
        }

        echo $form->render();

        $this->setView('create');
    }
}