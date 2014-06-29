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
	public function index()
	{
        die('hellotask');
        
        $res = $this->model('task')->index();
        if empty($res) {
        	$this->register('message', 'no task found');
        } else {
        	$this->register('message', 'All tasks :');
        }

        $this->setView('index');
	}

    public function find($id)
    {
        $res = $this->model('task')->find($id);
    }

    public function create()
    {
        $this->setView('create');

	    $form = new WalrusForm('form_task_create');
		echo $form->render();

        if (isset($_POST['type'])) {
            $res = $this->model('task')->create();
            
            if (isset($res['errors'])) {
                $this->register('errors', $res['errors']);
            }
            else
            {
                $this->go('/CleverManagement/task/');
            }
        }
    }

    public function delete($id)
    {
        $res = $this->model('task')->delete($id);
    }

    public function edit($id)
    {
        $res = $this->model('task')->edit($id);
    }
}
