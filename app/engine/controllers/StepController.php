<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
 * Class StepController
 * @package engine\controllers
 */
class StepController extends WalrusController
{
    public function show($id_project, $id_step)
    {
        $step = $this->model('step')->show($id_step);

        $this->register('step', $step);
        $this->register('project_id', $id_project);

        $task = $this->model('task')->index($id_step);
        if (empty($task))
        {
            $this->register('message', 'no task found');
        }
        else
        {
            $this->register('message', 'All tasks :');
        }

        $this->register('tasks', $task);

        $this->setView('show');
    }

    public function create($id_project)
    {
        $this->setView('create');

	    $form = new WalrusForm('form_step_create');
        $formAction = '/clevermanagement/'.$id_project.'/step/create';
        $form->setForm('action', $formAction);
		echo $form->render();

        if (!empty($_POST)) {
            $res = $this->model('step')->create($id_project);
            if (isset($res['errors'])) {
                $this->register('errors', $res['errors']);
            }
            else
            {
                $this->go('/clevermanagement/'.$id_project.'/show/');
            }
        }
    }

    public function delete($id_project, $id_step)
    {
        $res = $this->model('step')->delete($id_step);
        $this->go('/CleverManagement/'.$id_project.'/show');
    }

    public function edit($id_project, $id_step)
    {
        $this->setView('edit');

        $form = new WalrusForm('form_step_edit');
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/edit';
        $form->setForm('action', $formAction);

        $step = $this->model('step')->show($id_step);
        foreach ($form->getFields() as $field => $arrayOfAttribute) {
            if ($arrayOfAttribute['type'] == 'textarea') {
                $arrayOfAttribute['text'] = $step->getProperties()[$field];
            } else {
                $arrayOfAttribute['value'] = $step->getProperties()[$field];
            }
            $form->setFields($field, $arrayOfAttribute);
        }

        echo $form->render();

        if(!empty($_POST))
        {
            $res = $this->model('step')->edit($id_step);
            if(!empty($res['name.empty']))
            {
                $this->register('errors', $res);
            }
            else
            {
                $this->go('/CleverManagement/'.$id_project.'/show');
            }
        }

        $step = $this->model('step')->show($id_step);

        if(is_array($step))
        {
            $this->register('error', 'Step doesnt exist');
        }
    }
}
