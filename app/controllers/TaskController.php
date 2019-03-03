<?php

namespace app\controllers;

use app\Controller;
use app\App;
use app\entities\Tasks;
use app\lib\Paginator;


/**
 * Class SiteController
 * @package app\controlers
 */
class TaskController extends Controller
{
    public function beforeAction()
    {
        $page = App::getRequest('get', 'page');
        if ($page == 1) {
            $url = App::getRequest('path');
            $this->redirect($url);
        }
        parent::beforeAction();
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $cache = App::getComponent('cache');

        $cacheName = 'task_index' . App::getComponent('help')->multiImplode('_', App::getRequest('get')) .
            (App::getRequest('isAjax') ? '_ajax' : '') . (App::isGuest() ? '_guest' : '');

        $page = $cache->getOrSet($cacheName, function () {

            $sortBy = App::getRequest('get', 'sort');
            $page = App::getRequest('get', 'page');
            $direction = App::getRequest('get', 'order');

            if (!$sortBy) {
                $sortBy = 't.id';
                $direction = 'DESC';
            } else {
                $this->breadcrumbs[] = ['title' => 'Sort', 'url' => '/?sort=' . $sortBy . '&order=' . $direction];
            }

            if ($page > 1) {
                $this->breadcrumbs[] = ['title' => 'Page ' . $page];
            }

            $entityManager = App::getComponent('doctrine')->db;

            $query =  $entityManager->createQueryBuilder();
            $query->select('t', 'u')
                ->from('app\entities\Tasks', 't')
                ->leftJoin('t.user', 'u');

            if ($sortBy) {
                $query->orderBy($sortBy, strtoupper($direction));
            }
            $paginator = new Paginator($query, ['page' => $page]);

            $this->ajaxResponse = App::getRequest('isAjax');

            return $this->render('task/index', [
                'paginator' => $paginator,
            ]);
        });

        return $page;
    }

    /**
     * create task or render form
     * @param $params
     * @return string
     */
    public function actionCreate()
    {
        if (App::getRequest('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = App::getRequest('post');
            $validator = App::getComponent('validator');
            if (isset($postData['email'])) {
                $postData['email'] = strtolower($postData['email']);
            }
            $postData = $validator->clean($postData);
            $validateResult = $validator->validate($postData, Tasks::$createRules);
            if ($validateResult === true) {
                //write data

                $task = Tasks::createNew($postData);

                //redirect with flash data
                $this->redirect('/task/result', 302, [
                    'success' => $task != null,
                    'text' => $task != null ? 'Task was created successfully.' :
                        'There was error creating task.'
                ]);
            } else {
                //validate fails
                return $this->render('task/create', ['old' => $postData, 'errors' => $validateResult]);
            }
        }
        //start
        return $this->render('task/create');
    }

    /**
     * update task
     * @return stringp
     */
    public function actionUpdate()
    {
        $id = $this->actionParams['id'];
        $entityManager = App::getComponent('doctrine')->db;
        $task = $entityManager->find('app\entities\Tasks', $id);

        if (!$task) {
            return $this->actionNotfound();
        }
        if (App::getRequest('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = App::getRequest('post');
            $validator = App::getComponent('validator');
            $postData = $validator->clean($postData);
            $postData['status'] = $postData['status'] ? 1 : 0;
            $validateResult = $validator->validate($postData, Tasks::$updateRules);
            if ($validateResult === true) {
                //write data

                $result = $task->update($postData);

                //redirect with flash data
                $this->redirect('/task/result', 302, [
                    'success' => $result,
                    'text' => $result ? 'Task was updated.' :
                        'There was an error updating task.'
                ]);
            } else {
                //validate fails
                return $this->render('task/update', ['task'=> $task, 'old' => $postData, 'errors' => $validateResult]);
            }
        }
        return  $this->render('task/update', ['task' => $task]);
    }

    /**
     * get flash data and render result form
     * @return string
     */
    public function actionResult()
    {
        $session = App::getComponent('session');
        $success = $session->get('success');
        if ($success !== null) {
            $title = $success ? 'Successfull' : 'Error';
            $className = $success ? 'success' : 'error';
            $text = $session->get('text');
            return $this->render('results/result', ['className' => $className, 'title' => $title, 'text' => $text]);
        }
        $this->redirect('/');
    }

}
