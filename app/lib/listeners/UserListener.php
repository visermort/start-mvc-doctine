<?php

namespace app\lib\listeners;

use app\entities\Users;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{
    /** @PostPersist */
    public function postPersistHandler(Users $user, LifecycleEventArgs $event)
    {
        //todo send email to new user for example
    }
}