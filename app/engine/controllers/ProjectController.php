<?php

namespace app\engine\controllers;
 
use app\engine\controllers\CommonController;
use Walrus\core\WalrusForm;
use Walrus\core\WalrusACL;

/**
* Class ProjectController
* @package engine\controllers
*/
class ProjectController extends CommonController
{
    public function index()
    {
        if (empty($_SESSION))
        {
            $this->go('/CleverManagement/signin');
        }
        else
        {
            $res = $this->model('project')->index();
            $this->userDirectories();
            $this->register('projects', $res);
            $this->setView('index');
        }
    }

    public function create()
    {
        if (empty($_SESSION))
        {
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

            $this->userDirectories();
            $this->setView('create');

        }
    }

    public function edit($id)
    {
        var_dump('test0');
        if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
            return;
        }
        var_dump('test1');

        $this->userDirectories();
        $this->setView('edit');
        var_dump('test2');

        $form = new WalrusForm('form_project_edit');
        $formAction = '/clevermanagement/'.$id.'/edit';
        $form->setForm('action', $formAction);
        var_dump('test3');

        $project = $this->model('project')->find($id);
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

        var_dump('test4');

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

        var_dump('test5');

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
        if (empty($_SESSION))
        {
            $this->go('/CleverManagement/');
            return;
        }

        $res = $this->model('project')->show($id);
        $this->register('project', $res);

        $step = $this->model('step')->index($id);
        if (empty($step))
            $this->register('message', "Pas d'etape trouvee pour ce projet");
        else
            $this->register('message', 'Etapes :');

        $admins = $this->model('project')->retrieveUsers($id, 'additionalAdmins');
        $members = $this->model('project')->retrieveUsers($id, 'members');
        $status = $this->model('project')->status_step($id);
        $time_project = $this->model('project')->time_project($id);

        $this->userDirectories();
        $this->register('hour_project', $time_project['hour']);
        $this->register('price_project', $time_project['price']);
        $this->register('project_id', $id);
        $this->register('steps', $step);
        $this->register('admins', $admins);
        $this->register('members', $members);
        $this->register('status', $status);

        $this->setView('show');
    }
}
