<?php

namespace app\controllers;

use app\Controller;
use app\App;
use app\entities\Users;

/**
 * Class SiteController
 * @package app\controlers
 */
class AccountController extends Controller
{
    /**
     * create task or render form
     * @param $params
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('results/result', [
            'title'=> 'Under constuction',
            'text'=> 'Sorry. This page is under consruction! Please, visit it soon',
            'className' => 'success',
        ]);
    }
    /**
     * create task or render form
     * @param $params
     * @return string
     */
    public function actionLogout()
    {
        $auth = App::getComponent('auth');
        $auth->logout();
        return $this->redirect('/');
    }

    /**
     * @param $params
     * @return string
     */
    public function actionLogin()
    {
        $auth = App::getComponent('auth');
        $user = $auth->getUser();
        if ($user) {
            $this->redirect('/');
        }
        $request = App::getComponent('request');
        if ($request->get('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = $request->get('post');
            $validator = App::getComponent('validator');
            $postData = $validator->clean($postData);
            $validateResult = $validator->validate($postData, Users::$rulesLogin);
            if ($validateResult === true) {
                $user = $auth->authenticate($postData);
                if ($user) {
                    $auth->login($user);
                    $this->redirect(App::getComponent('config')->get('app.account_start_page'));
                }
                $validateResult = [
                    'password' => 'You user name or password are invalid',
                    'name' => 'You user name or password are invalid',
                ];
            }
            return $this->render('account/login', ['old' => $postData, 'errors' => $validateResult]);
        }
        //start
        return $this->render('account/login');
    }

    /**
     * @return string
     */
    public function actionRegister()
    {
        $auth = App::getComponent('auth');
        $user = $auth->getUser();
        if ($user) {
            $this->redirect('/');
        }
        $request = App::getComponent('request');
        if ($request->get('method') == 'POST') {
            //if post
            // validate and clean post data
            $postData = $request->get('post');
            $validator = App::getComponent('validator');
            $postData = $validator->clean($postData);
            $validateResult = $validator->validate($postData, Users::$rulesRegister);
            if ($validateResult === true) {
                $user = $auth->register($postData);
                if ($user) {
                    $auth->login($user);
                    $this->redirect(App::getComponent('config')->get('app.account_start_page'));
                }
                if ($user === false) {
                    $validateResult = [
                        'email' => 'This email is used. Please <a href="/login">login</a> or do password recowery',
                    ];
                }
            }
            return $this->render('account/register', ['old' => $postData, 'errors' => $validateResult]);
        }
        //start
        return $this->render('account/register');
    }


}
