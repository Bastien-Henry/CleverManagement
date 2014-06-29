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
        // $this->register('message', 'no project found');
        // } else {
        // $this->register('message', 'All projects :');
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
            $this->go('/CleverManagement');
        }

        echo $form->render();

        $this->setView('create');
    }

    public function edit($id)
    {
        if(!empty($_POST))
        {
            $res = $this->model('project')->edit($id);
            if(!empty($res['name.empty']))
            {
                $this->register('errors', $res);
            }
            else
            {
                $this->go('/CleverManagement');
            }
        }

        $res = $this->model('project')->show($id);

        if(is_array($res))
        {
            $this->register('error', 'Project doesnt exist');
        }
        else
        {
            $this->register('project', $res);
        }

        $this->setView('edit');
    }

    public function delete($id)
    {
        $res = $this->model('project')->delete($id);

        $this->go('/CleverManagement');
    }

    public function show($id)
    {
        $res = $this->model('project')->show($id);

        $this->register('project', $res);

        $this->setView('show');
    }
}