<?php

namespace app\engine\controllers;
 
use app\engine\controllers\CommonController;
use Walrus\core\WalrusForm;

/**
* Class ProjectController
* @package engine\controllers
*/
class SessionController extends CommonController
{
    public function create($id_project, $id_step, $id_task)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $form = new WalrusForm('form_session_create');
            $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/session/create';
            $form->setForm('action', $formAction);
            
            if(!empty($_POST))
            {
               if (!isset($form->check()['hour_number']) && !isset($form->check()['percent']) && !isset($form->check()['comment']))
               {
                    $this->model('session')->create($id_project, $id_task);
                    $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
               }
            }
            $this->register('project_id', $id_project);
            $this->register('step_id', $id_step);
            $this->register('task_id', $id_task);
            $this->register('myFormCreate', $form->render());

            $this->userDirectories();
            $this->setView('create');
        }
    }

    public function delete($id_project, $id_step, $id_task, $id_session)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $res = $this->model('session')->delete($id_project, $id_session);
            $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
        }
    }

    public function edit($id_project, $id_step, $id_task, $id_session)
    {
        if (empty($_SESSION)) 
        {
            $this->go('/CleverManagement/');
        }
        else
        {
            $this->setView('edit');

            $form = new WalrusForm('form_session_edit');
            $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/session/'.$id_session.'/edit';
            $form->setForm('action', $formAction);

            $session = $this->model('session')->show($id_project, $id_session);
            foreach ($form->getFields() as $field => $arrayOfAttribute)
            {
                if ($arrayOfAttribute['type'] == 'textarea') {
                    $arrayOfAttribute['text'] = $session->getProperties()[$field];
                } else {
                    $arrayOfAttribute['value'] = $session->getProperties()[$field];
                }
                $form->setFields($field, $arrayOfAttribute);
            }
            
            $this->register('project_id', $id_project);
            $this->register('step_id', $id_step);
            $this->register('task_id', $id_task);
            $this->register('myFormEdit', $form->render());

            if(!empty($_POST))
            {
                var_dump($form->check());
                if (!isset($form->check()['hour_number']) && !isset($form->check()['percent']) && !isset($form->check()['comment']))
                {
                    $session = $this->model('session')->edit($id_project, $id_task, $id_session);
                    $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
                }
            }

            $this->userDirectories();
            $session = $this->model('session')->show($id_project, $id_session);

            if(is_array($session))
            {
                $this->register('error', 'Session doesnt exist');
            }
        }
    }
}