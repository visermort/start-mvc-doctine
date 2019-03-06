<?php

namespace app\controllers;

use app\Controller;
use app\App;
use app\entities\Tasks;
use app\lib\paginator\Paginator;


/**
 * Class SiteController
 * @package app\controlers
 */
class TaskController extends Controller
{
    public function beforeAction()
    {
        $page = App::getComponent('request')->get('get.page');
        if ($page == 1) {
            $url = App::getComponent('request')->get('path');
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
        $request = App::getComponent('request');
        $auth = App::getComponent('auth');

        $cacheName = 'task_index' . App::getComponent('help')->multiImplode('_', $request->get('get')) .
            ($request->get('isAjax') ? '_ajax' : '') . ($auth->isGuest() ? '_guest' : '');

        $page = $cache->getOrSet($cacheName, function () use ($request) {

            $sortBy = $request->get('get.sort');
            $page = $request->get('get.page');
            $direction = $request->get('get.order');

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
            $paginator = new Paginator($query, ['page' => $page, 'link_classes' => ['ajax-button']]);

            $this->ajaxResponse = $request->get('isAjax');

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
        $request = App::getComponent('request');
        if ($request->get('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = $request->get('post');
            $validator = App::getComponent('validator');
            $postData = $validator->clean($postData);
            $validateResult = $validator->validate($postData, Tasks::$createRules);
            if ($validateResult === true) {
                //write data

                $task = Tasks::createNew($postData);
                if ($task === false) {
                    //wrong user_id
                    $this->redirect('/task/result', 302, [
                        'success' => false,
                        'text' => 'There was error creating task. Wrong user.',
                    ]);
                } else {
                    //redirect with flash data
                    $this->redirect('/task/result', 302, [
                        'success' => $task != null,
                        'text' => $task != null ? 'Task was created successfully.' :
                            'There was error creating task.'
                    ]);
                }
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
        $request = App::getComponent('request');

        if (!$task) {
            return $this->actionNotfound();
        }
        if ($request->get('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = $request->get('post');
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

    /**
     * view task
     * @return string
     */
    public function actionView()
    {
        $id = $this->actionParams['id'];
        $entityManager = App::getComponent('doctrine')->db;
        $task = $entityManager->find('app\entities\Tasks', $id);
        if (!$task) {
            $this->actionNotfound();
        }
        $this->breadcrumbs[] = ['title' => $task->getId()];
        return  $this->render('task/view', ['task' => $task]);
    }

}
