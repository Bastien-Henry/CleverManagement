<?php

namespace app\engine\models;

use R;
use app\engine\models\Common;

class Project extends Common
{
    public function create()
    {
        // project creation
        $project = R::dispense('projects');
        $project->name = $_POST['name'];
        $project->startline = $_POST['startline'];
        $project->deadline = $_POST['deadline'];
        $project->description = $_POST['description'];
        $id = R::store($project);

        // link to current user as admin
        R::exec('INSERT INTO projects_users (id_user, id_project, admin) VALUES (:user, :project, 1)', array(
            ':user' => $_SESSION['user']['id'], 
            ':project' => $id)
        );

        // link to specified members
        $membersErrors = $this->membersFlush($_POST['members'], $id, 0);
        $adminsErrors = $this->membersFlush($_POST['additionalAdmins'], $id, 1);
        $directoryError = $this->directoryFlush($_POST['directory'], $id);

        return true;
    }

    /*
        Function user for member and admin select2 fields

        Connects given email addresses to project as member or admin

        return value : Array of email addresses which failed to connect to project
    */
    private function membersFlush($selects, $id_project, $boolAdmin)
    {
        if(empty($selects) || $selects == "") {
            return;
        }
        $members = $this->parseSelects($selects);

        $memberErrors = array();

        foreach ($members as $value => $status) {
            if ($status === 1) {
                $member = R::findOne('users', 'email = ?', array($value));

                if ($member == null || !is_object($member)) {
                    $memberErrors[] = $value;
                    continue;
                }

                $query = 'SELECT * FROM projects_users WHERE id_user = :user AND id_project = :project AND admin = :admin';
                $params = array(':user' => $member->getProperties()['id'], ':project' => $id_project, ':admin' => $boolAdmin);
                $relation = R::getRow($query, $params);

                if ($relation != null) {
                    continue;
                }

                $exec = R::exec('INSERT INTO projects_users (id_user, id_project, admin) VALUES (:user, :project, :admin)', array(
                    ':user'     => $member->getProperties()['id'], 
                    ':project'  => $id_project,
                    ':admin'    => $boolAdmin
                ));
            } else {
                $memberErrors[] = $value;
            }
        }

        return $memberErrors;
    }

    /*
        Function used for member and admin select2 fields

        Checks email address pattern

        return value : Array of every email addresses given as key and 1 or 0 as value
        1 means email address is good
        0 means email address is wrong
    */
    private function parseSelects($selects)
    {
        $regex = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
        $values = explode(',', $selects);
        $rightValues = array();
        $errors = array();
        foreach ($values as $key => $value) {
            if (preg_match($regex, $value) === 1) {
                $return[$value] = 1;
            } else {
                $return[$value] = 0;
            }
        }

        return $return;
    }

    public function find($id)
    {
        $project = R::load('projects', $id);

        if($project->getProperties()['id'] == 0)
        {
            return array('project.not_found' => 'project doesnt exist');
        }

        return $project;
    }

    public function show($id)
    {
        $this->permission_access($id);
        $project = R::load('projects', $id);

        if($project->getProperties()['id'] == 0)
        {
            return array('project.not_found' => 'project doesnt exist');
        }

        $relation = R::getRow('SELECT * FROM projects_users WHERE id_user = :user AND id_project = '.$id.'',
                [':user' => $_SESSION['user']['id']]
            );
        $tab = array();
        if($relation['admin'] == 1)
            $tab['admin'] = $project;
        else
            $tab['member'] = $project;
    
        return $tab;
    }

    public function index()
    {
        $user = R::load('users', $_SESSION['user']['id']);
        $relations = R::getAll('SELECT * FROM projects_users WHERE id_user = :user',
            [':user' => $user->getProperties()['id']]
        );

        $projects = array();
        foreach ($relations as $key => $object)
        {
            if($object['admin'])
                $projects['admin'][] = R::load('projects', $object['id_project']);
            else
                $projects['member'][] = R::load('projects', $object['id_project']);

            $this->status_step($object['id_project']);
        }

        return $projects;
    }

    public function edit($id)
    {
        $this->permission_exec($id);
        $project = R::load('projects', $id);

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $project->name = $_POST['name'];
        $project->description = $_POST['description'];
        $project->startline = $_POST['startline'];
        $project->deadline = $_POST['deadline'];

        //link to specified members
        $membersErrors = $this->membersFlush($_POST['members'], $id, 0);
        $adminsErrors = $this->membersFlush($_POST['additionalAdmins'], $id, 1);
        $this->directoryFlush($_POST['directory'], $id);
        R::store($project);

        return $project;
    }

    public function retrieveUsers($id_project, $field = null)
    {
        $query = 'SELECT * FROM projects_users WHERE id_project = :project';
        $params = array(':project' => $id_project);

        if ($field != null) {
            if ($field == "additionalAdmins") {
                $status = 1;
            } else {
                $status = 0;
            }

            $query .= ' AND admin = :status';
            $params[':status'] = $status;
        }

        $relations = R::getAll($query, $params);

        $users = array();
        foreach ($relations as $key => $object) {
            $users[] = R::load('users', $object['id_user']);
        }

        return $users;
    }

    public function retrieveUsersEmails($id_project, $field = null)
    {
        $users = $this->retrieveUsers($id_project, $field);

        $usersEmail = array();
        foreach ($users as $key => $user) {
            $usersEmail[] = $user->getProperties()['email'];
        }

        return $usersEmail;
    }

    public function delete($id)
    {
        $this->permission_exec($id);

        $project = R::load('projects', $id);

        R::trash($project);
    }

    public function getDirectory($id)
    {
        $userDirectories = R::getAll('SELECT * FROM directories WHERE id_user = :user', array(':user' => $_SESSION['user']['id']));
        $query = 'SELECT * FROM projects_directories WHERE id_project = :project AND id_directory = :directory';

        $foundDir = false;
        foreach ($userDirectories as $directory) {
            $params = array(':project' => $id, ':directory' => $directory['id']);
            $match = R::getAll($query, $params);
            if (!empty($match)) {
                $foundDir[] = $match;
            }
        }

        if ($foundDir === false) {
            return $foundDir;
        } elseif (count($foundDir) > 1) {
            $this->deleteDirectories($id);
            return false;
        } else {
            return $foundDir[0][0]['id_directory'];
        }
    }

    public function deleteDirectories($id)
    {
        $userDirectories = R::getAll('SELECT * FROM directories WHERE id_user = :user', array(':user' => $_SESSION['user']['id']));
        if (!is_array($userDirectories) || empty($userDirectories)) {
            return;
        }

        $query = 'DELETE FROM projects_directories WHERE id_project = :project AND id_directory = :directory';
        
        foreach ($userDirectories as $directory) {
            $params = array(':project' => $id, ':directory' => $directory['id']);
            R::exec($query, $params);
        }
    }

    public function directoryFlush($name, $id_project)
    {
        $directory = R::findOne('directories', 'name = :name AND id_user = :user', array(':name' => $name, ':user' => $_SESSION['user']['id']));
        if (!is_object($directory)) {
            return 'Le dossier n\'existe pas';
        }

        $this->deleteDirectories($id_project);

        $exec = R::exec('INSERT INTO projects_directories (id_project, id_directory) VALUES (:project, :directory)', array(
            ':project'  => $id_project,
            ':directory'     => $directory->getProperties()['id'], 
        ));

        return true;
    }
}