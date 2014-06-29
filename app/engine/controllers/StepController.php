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
	public function index()
	{
		die('hello');

        $res = $this->model('step')->index();
        if empty($res) {
        	$this->register('message', 'no step found');
        } else {
        	$this->register('message', 'All Steps :');
        }

        $this->setView('index');
	}

    public function find($id)
    {
        $res = $this->model('step')->find($id);

        $this->setView('show');
    }

    public function create()
    {
        $this->setView('create');

	    $form = new WalrusForm('form_step_create');
		echo $form->render();

        if (isset($_POST['type'])) {
            $res = $this->model('step')->create();
            
            if (isset($res['errors'])) {
                $this->register('errors', $res['errors']);
            }
            else
            {
                $this->go('/CleverManagement/step/');
            }
        }
    }

    public function delete($id)
    {
        $res = $this->model('step')->delete($id);
    }

    public function edit($id)
    {
        $res = $this->model('step')->edit($id);
    }
}
