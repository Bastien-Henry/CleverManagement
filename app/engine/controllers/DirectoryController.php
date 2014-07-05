<?php

namespace app\engine\controllers;

use app\engine\controllers\CommonController;
use Walrus\core\WalrusForm;

/**
 * Class DirectoryController
 * @package engine\controllers
 */
class DirectoryController extends CommonController
{
	public function show($id)
	{
		$this->userDirectories();
        $directory = $this->model('directory')->getDirectory($id);
        $res = $this->model('directory')->show($id);
        $this->register('projects', $res);
        $this->register('myDirectory', $directory);
        $this->setView('show');
	}

	public function create()
	{
		$form = new WalrusForm('form_directory_create');
        $this->register('myFormCreate', $form->render());
        // $form->check();
        if(!empty($_POST))
        {
            $this->model('directory')->create();
            $this->go('/CleverManagement');
        }

        $this->userDirectories();
        $this->setView('create');
	}

	public function edit($id)
	{
		$this->userDirectories();
		$this->setView('edit');

        $form = new WalrusForm('form_directory_edit');
        $formAction = '/clevermanagement/directory/'.$id.'/edit';
        $form->setForm('action', $formAction);

        $directory = $this->model('directory')->getDirectory($id);
        $form->setFieldValue('name', 'value', $directory->getProperties()['name']);

        $form->check();
        $this->register('myFormEdit', $form->render());

        if(!empty($_POST))
        {
            $res = $this->model('directory')->edit($id);
            if(!empty($res['name.empty']))
            {
                $this->register('errors', $res);
            }
            else
            {
                $this->go('/CleverManagement');
            }
        }

        $res = $this->model('directory')->show($id);

        if(is_array($res))
        {
            $this->register('error', 'Project doesnt exist');
        }

	}

	public function delete($id)
	{
        $res = $this->model('directory')->delete($id);
        $this->go('/CleverManagement');
	}
}