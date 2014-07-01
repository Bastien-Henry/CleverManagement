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
        $this->setView('edit');

        $form = new WalrusForm('form_project_edit');
        $formAction = '/clevermanagement/'.$id.'/edit';
        $form->setForm('action', $formAction);

        $project = $this->model('project')->show($id);
        foreach ($form->getFields() as $field => $arrayOfAttribute) {
            if ($arrayOfAttribute['type'] == 'textarea') {
                $arrayOfAttribute['text'] = $project->getProperties()[$field];
            } else {
                $arrayOfAttribute['value'] = $project->getProperties()[$field];
            }
            
            $form->setFields($field, $arrayOfAttribute);
        }

        echo $form->render();

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

        $step = $this->model('step')->index($id);
        if (empty($step)) {
            $this->register('message', "Pas d'etape trouvee pour ce projet");
        } else {
            $this->register('message', 'Etapes :');
        }

        $this->register('steps', $step);

        $this->setView('show');
    }
}