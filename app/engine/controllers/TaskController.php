<?php

namespace app\engine\controllers;

use app\engine\controllers\CommonController;
use Walrus\core\WalrusForm;

/**
 * Class TaskController
 * @package engine\controllers
 */
class TaskController extends CommonController
{
    public function show($id_project, $id_step, $id_task)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }

        $task = $this->model('task')->show($id_project, $id_task);
        $members = $this->model('task')->retrieveMembers($id_task);
        $time_task = $this->model('task')->time_task($id_task);

        $this->userDirectories();
        $this->register('hour_task', $time_task['hour']);
        $this->register('price_task', $time_task['price']);
        $this->register('task', $task);
        $this->register('task_id', $id_task);
        $this->register('step_id', $id_step);
        $this->register('project_id', $id_project);
        $this->register('members', $members);

        $session = $this->model('session')->index($id_project, $id_task);

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

    /*
        For the form :
            Get email addresses of every member of the project via the 
            retrieveUsersEmails method from project model
            Set the members field options (it is multiple select) with email addresses
            as key and value (using array_combine php method) with WalrusForm
            setFieldValue() method
    */
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
            $this->userDirectories();
            
            if (!empty($_POST)) {
                $res = $this->model('task')->create($id_project, $id_step);
                
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
            $this->model('task')->delete($id_project, $id_task);

            $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/show');
        }
    }

    /*
        For the form :
            Set value to every field with registered values in database
            Get every member of the task to give the possibility to unlink them 
            (registeredMembers)
            Get every member of the project who are not related to the task to
            give the possibility to add them (members)
    */
    public function edit($id_project, $id_step, $id_task)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }
        $this->userDirectories();

        if(!empty($_POST))
        {
            $task = $this->model('task')->edit($id_project, $id_task);
            if(!empty($task['name.empty']))
            {
                $this->register('errors', $task);
            }
            else
            {
                $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/show');
            }

            $task = $this->model('task')->show($id_project, $id_task);

            if(is_array($task))
            {
                $this->register('error', 'Task doesnt exist');
            }
        } else {
            $this->setView('edit');

            $form = new WalrusForm('form_task_edit');

            $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/edit';
            $form->setForm('action', $formAction);

            $task = $this->model('task')->show($id_project, $id_task);

            // filling fields with values registered in database
            // different treatment depending on type of field
            foreach ($form->getFields() as $field => $arrayOfAttribute) {
                if ($field == 'registeredMembers') {
                    $function = 'TaskController::getRegisteredMembers([ 0:'.$id_task.'])';
                    $form->setFieldValue('registeredMembers', 'function', $function);
                } elseif ($field == 'members') {
                    $membersProject = $this->model('project')->retrieveUsers($id_project, null);
                    $membersTask = $this->model('task')->retrieveMembers($id_task);
                    $availableMembers = $this->model('task')->availableMembersEmails($membersProject, $membersTask);
                    $preparedArray = array_combine($availableMembers, $availableMembers);
                    $form->setFieldValue('members', 'options', $preparedArray);
                } elseif ($arrayOfAttribute['type'] == 'date') {
                    //to do 
                    //$date = date("d-m-Y", strtotime($task->getProperties()[$field]));
                    //$form->setFieldValue($field, 'value', $date);
                } elseif ($arrayOfAttribute['type'] == 'checkbox') {
                    //to do
                    //$form->setFieldValue($field, '', $task->getProperties()[$field]);
                } elseif ($arrayOfAttribute['type'] == 'textarea') {
                    $form->setFieldValue($field, 'text', $task->getProperties()[$field]);
                } else {
                    $form->setFieldValue($field, 'value', $task->getProperties()[$field]);
                }
            }

            $form->check();
            $this->register('myFormEdit', $form->render());
        }
    }

    public function getRegisteredMembers($id_task)
    {
        $members = $this->model('task')->retrieveMembers($id_task);
        $options = $this->model('task')->formatMembers($members);
        return $options;
    }
}
