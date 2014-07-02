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
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }

        $task = $this->model('task')->show($id_task);
        $members = $this->model('task')->retrieveMembers($id_task);

        $this->register('task', $task);
        $this->register('step_id', $id_step);
        $this->register('project_id', $id_project);
        $this->register('members', $members);

        $session = $this->model('session')->index($id_task);

        if (empty($session))
        {
            $this->register('message', 'Pas de sessions pour cette tâche');
        }
        else
        {
            $this->register('message', 'Toutes les sessions :');
        }

        $this->register('sessions', $session);

        $this->setView('show');
    }

    public function create($id_project, $id_step)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $this->setView('create');

            $form = new WalrusForm('form_task_create');
            $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/create';
            $form->setForm('action', $formAction);
            $availableMembers = $this->model('project')->retrieveUsersEmails($id_project, null);
            $preparedArray = array_combine($availableMembers, $availableMembers);
            $form->setFieldValue('members', 'options', $preparedArray);
            $this->register('myFormCreate', $form->render());

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
    }

    public function delete($id_project, $id_step, $id_task)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $this->model('task')->delete($id_task);

            $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/show');
        }
    }

    public function edit($id_project, $id_step, $id_task)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }

        $this->setView('edit');

        $form = new WalrusForm('form_task_edit');
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/edit';
        $form->setForm('action', $formAction);

        $task = $this->model('task')->show($id_task);
        foreach ($form->getFields() as $field => $arrayOfAttribute) {
            if ($field == 'members') {
                $usersEmail = $this->model('task')->retrieveMembers($id_task);
                $form->setFieldValue($field, 'value', implode(',', $usersEmail));
            } elseif ($arrayOfAttribute['type'] == 'textarea') {
                $arrayOfAttribute['text'] = $task->getProperties()[$field];
            } else {
                $arrayOfAttribute['value'] = $task->getProperties()[$field];
            }
        }
        
        $form->setFields($field, $arrayOfAttribute);

        $this->register('myFormEdit', $form->render());

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
