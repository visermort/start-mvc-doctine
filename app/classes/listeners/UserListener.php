<?php

namespace app\classes\listeners;

use app\entities\Users;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class UserListener
 * @package app\classes\listeners
 */
class UserListener
{
    /** @PostPersist */
    public function postPersistHandler(Users $user, LifecycleEventArgs $event)
    {
        //todo send email to new user for example
    }
}