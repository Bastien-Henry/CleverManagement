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
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/signin');
        }
        else {
            $res = $this->model('project')->index();
            $this->register('projects', $res);
            $this->setView('index');
        }
    }

    public function create()
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $form = new WalrusForm('form_project_create');
            $this->register('myFormCreate', $form->render());
            // $form->check();
            if(!empty($_POST))
            {
                $this->model('project')->create();
                $this->go('/CleverManagement');
            }

            $this->setView('create');

        }
    }

    public function edit($id)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }

        $this->setView('edit');

        $form = new WalrusForm('form_project_edit');
        $formAction = '/clevermanagement/'.$id.'/edit';
        $form->setForm('action', $formAction);

        $project = $this->model('project')->show($id);
        foreach ($form->getFields() as $field => $arrayOfAttributes) {
            if ($field == 'members' || $field == 'additionalAdmins') {
                $usersEmail = $this->model('project')->retrieveUsersEmails($id, $field);
                $form->setFieldValue($field, 'value', implode(',', $usersEmail));
            } elseif ($arrayOfAttributes['type'] == 'textarea') {
                $form->setFieldValue($field, 'text', $project->getProperties()[$field]);
            } else {
                $form->setFieldValue($field, 'value', $project->getProperties()[$field]);
            }
        }

        $this->register('myFormEdit', $form->render());

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
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
            $res = $this->model('project')->delete($id);

            $this->go('/CleverManagement');
        }
    }

    public function show($id)
    {
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }

        $res = $this->model('project')->show($id);
        $this->register('project', $res);

        $step = $this->model('step')->index($id);
        if (empty($step)) {
            $this->register('message', "Pas d'etape trouvee pour ce projet");
        } else {
            $this->register('message', 'Etapes :');
        }

        $admins = $this->model('project')->retrieveUsers($id, 'additionalAdmins');
        $members = $this->model('project')->retrieveUsers($id, 'members');
        $status = $this->model('project')->status($id);
        $time_project = $this->model('project')->time_project($id);

        $this->register('time_project', $time_project);
        $this->register('steps', $step);
        $this->register('admins', $admins);
        $this->register('members', $members);
        $this->register('status', $status);

        $this->setView('show');
    }
}
