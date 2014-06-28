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
    public function index()
    {
        $res = $this->model('project')->index();
        // if empty($res) {
        //     $this->register('message', 'no project found');
        // } else {
        //     $this->register('message', 'All projects :');
        // }

        // var_dump($res);
        // exit();

        $this->register('projects', $res);

        $this->setView('index');
    }

    public function create()
    {
    	$form = new WalrusForm('form_project_create');
    	// $form->check();
    	if(!empty($_POST))
    	{
    		$this->model('project')->create();
            return $this->index();
    	}

    	echo $form->render();

    	$this->setView('create');
    }

    public function delete($id)
    {
        $result = $this->model('project')->delete($id);

        return $this->index();
    }
}