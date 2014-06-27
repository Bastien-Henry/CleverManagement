<?php

namespace app\engine\controllers;
 
use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
 * Class ProjectController
 * @package engine\controllers
 */
class ProjectController extends WalrusController
{
    public function run()
    {
    	$test = "Ceci est un test";

    	$this->register('test', $test);
    	$this->setView('index');
    }

    public function create()
    {
    	$form = new WalrusForm('form_project_create');
    	// $form->check();
    	if(!empty($_POST))
    	{
    		var_dump($_POST);
    		$this->model('project')->create();
    	}

    	echo $form->render();

    	$this->setView('create');
    }

    public function delete($id)
    {
        $result = $this->model('project')->delete($id);

        if(!empty($result))
        {
            $this->register('error', $result);
            $this->go('/CleverManagement/project/'.$id);
        }

        $this->go('/CleverManagement/');
    }
}