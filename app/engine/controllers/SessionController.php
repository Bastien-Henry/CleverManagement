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
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/session/create';
        $form->setForm('action', $formAction);
        
        if(!empty($_POST))
        {
            $this->model('session')->create($id_task);
            $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
        }
        $this->register('myFormCreate', $form->render());

        $this->setView('create');
    }

    public function delete($id_project, $id_step, $id_task, $id_session)
    {
        $res = $this->model('session')->delete($id_session);
        $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
    }

    public function edit($id_project, $id_step, $id_task, $id_session)
    {
        $this->setView('edit');

        $form = new WalrusForm('form_session_edit');
        $formAction = '/clevermanagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/session/'.$id_session.'/edit';
        $form->setForm('action', $formAction);

        $session = $this->model('session')->show($id_session);
        foreach ($form->getFields() as $field => $arrayOfAttribute)
        {
            if ($arrayOfAttribute['type'] == 'textarea')
            {
                $arrayOfAttribute['text'] = $session->getProperties()[$field];
            }
            else
            {
                $arrayOfAttribute['value'] = $session->getProperties()[$field];
            }
            $form->setFields($field, $arrayOfAttribute);
        }

        $this->register('myFormEdit', $form->render());

        if(!empty($_POST))
        {
            $session = $this->model('session')->edit($id_session);
            $this->go('/CleverManagement/'.$id_project.'/step/'.$id_step.'/task/'.$id_task.'/show');
        }

        $session = $this->model('session')->show($id_session);

        if(is_array($session))
        {
            $this->register('error', 'Session doesnt exist');
        }
    }
}