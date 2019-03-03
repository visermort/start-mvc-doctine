<?php

namespace app\Components;

use app\App;
use app\Component;
use app\entities\Users;

/**
 * Class Auth
 * @package app\Components
 */
class Auth extends Component
{
    /**
     * check login
     */
    public static function init()
    {
        try {
            $sessionKey = App::getComponent('session')->get(App::getConfig('app.session_user_key'));
            if ($sessionKey) {
                $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
                $user = $usersRepository->findOneBy(['sessionKey' => $sessionKey]);
                if ($user) {
                    App::setUser($user);
                }
            }
        } catch (\Exception $e) {
            if (App::getConfig('app.debug')) {
                echo $e->getMessage();
            }
        }
        return parent::init();
    }

    /**
     * @param $credentials
     * @return mixed
     */
    public function register($credentials)
    {
        $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
        $user = $usersRepository->findOneBy(['email' => $credentials['email']]);
        if ($user) {
            //email allredy in use
            return false;
        }
        $credentials['password'] = password_hash($credentials['password'], PASSWORD_DEFAULT);
        $user = Users::create('app\entities\Users', $credentials);
        return $user;
    }

    /**
     * @param $credentials
     * @return mixed
     */
    public function authenticate($credentials)
    {
        $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
        $user = $usersRepository->findOneBy(['email' => $credentials['email']]);
        if ($user && password_verify($credentials['password'], $user->getPassword())) {
            return $user;
        }
    }

    /**
     * @param $user
     * @return mixed
     */
    public function login($user)
    {
        App::setUser($user);
        $sessionKey = bin2hex(random_bytes(16));
        $user->update(['session_key' => $sessionKey, 'last_login' => new \DateTime()]);
        App::getComponent('session')->set(App::getConfig('app.session_user_key'), $sessionKey);
        return $user;
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $user = App::getUser();
        if ($user) {
            $user->update(['session_key' => null]);
            App::setUser(null);
        }

        App::getComponent('session')->destroy();
    }

    /**
     * set permission
     * @param $email
     * @param $role
     * @param bool $value
     * @return bool|array
     */
    public function setUserPermision($email, $newPermissions = [])
    {
        $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
        $user = $usersRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return false;
        }
        $permissions = json_decode($user->getPermissions(), true);
        $permissions = empty($permissions) ? [] : $permissions;
        $permissions = array_merge($permissions, $newPermissions);
        if ($user->update(['permissions' => json_encode($permissions)])) {
            return $permissions;
        }
    }

    /** revoke all permissions
     * @param $email
     * @return null
     */
    public function revokeUserPermisions($email)
    {
        $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
        $user = $usersRepository->findOneBy(['email' => $email]);
        $user->update(['permissions' => null]);
    }

}
