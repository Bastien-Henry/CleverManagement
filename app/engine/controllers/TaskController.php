<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
 * Class TaskController
 * @package engine\controllers
 */
class TaskController extends WalrusController
{
    public function show($id_project, $id_step, $id_task)
    {
        $task = $this->model('task')->show($id_task);

        $this->register('task', $task);
        $this->register('step_id', $id_step);
        $this->register('project_id', $id_project);

        $session = $this->model('session')->index($id_task);

        if (empty($session))
        {
            $this->register('message', 'no session found');
        }
        else
        {
            $this->register('message', 'All sessions :');
        }

        $this->register('sessions', $session);

        $this->setView('show');
    }

    public function create($id_project, $id_step)
    {
        $this->setView('create');

	    $form = new WalrusForm('form_task_create');
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/create';
        $form->setForm('action', $formAction);
		echo $form->render();

        if (!empty($_POST)) {
            $res = $this->model('task')->create($id_step);
            
            if (isset($res['errors']))
            {
                $this->register('errors', $res['errors']);
            }
            else
            {
                $this->go('/clevermanagement/'.$id_project.'/step/'.$id_step.'/show');
            }
        }
    }

    public function delete($id_project, $id_step, $id_task)
    {

        $this->model('task')->delete($id_task);

        $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/show');
    }

    public function edit($id_project, $id_step, $id_task)
    {
        $this->setView('edit');

        $form = new WalrusForm('form_task_edit');
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/edit';
        $form->setForm('action', $formAction);

        $task = $this->model('task')->show($id_task);
        foreach ($form->getFields() as $field => $arrayOfAttribute) {
            if ($arrayOfAttribute['type'] == 'textarea') {
                $arrayOfAttribute['text'] = $task->getProperties()[$field];
            } else {
                $arrayOfAttribute['value'] = $task->getProperties()[$field];
            }
            $form->setFields($field, $arrayOfAttribute);
        }

        echo $form->render();

        if(!empty($_POST))
        {
            $task = $this->model('task')->edit($id_task);
            if(!empty($task['name.empty']))
            {
                $this->register('errors', $task);
            }
            else
            {
                $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/show');
            }
        }

        $task = $this->model('task')->show($id_task);

        if(is_array($task))
        {
            $this->register('error', 'Task doesnt exist');
        }
    }
}
