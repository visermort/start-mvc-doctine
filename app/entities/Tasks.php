<?php

namespace app\entities;

use app\App;
use app\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tasks
 */
class Tasks extends Entity
{
    public static $createRules = [
        'required'=> [['first_name', 'email', 'text', 'csrf']],
        'email' => [['email']],
    ];
    public static $updateRules = [
        'required'=> [['text', 'csrf']],
    ];


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }


    /**
     * @param $data
     * @return null
     */
    public static function createNew($data)
    {
        try {
            $usersRepository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
            $user = $usersRepository->findOneBy(['email' => $data['email']]);
            if (!$user) {
                $user = static::create('app\entities\Users', $data, false);
            }
            $data['user'] = $user;
            $task = static::create('app\entities\Tasks', $data);
            return $task;
        } catch (\Exception $e) {
            if (App::getConfig('app.debug')) {
                d($e);
            }
        }
    }


    /**
     * @var string
     */
    private $text;

    /**
     * @var boolean
     */
    private $status = '0';

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \app\entities\Users
     */
    private $user;


    /**
     * Set text
     *
     * @param string $text
     *
     * @return Tasks
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Tasks
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Tasks
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get setUpdateddAt
     *
     * @return Tasks
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \app\entities\Users $user
     *
     * @return Tasks
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \app\entities\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * update $updatedAt
     */
    public function doPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
    /**
     * clear cache after updating
     */
    public function doPostUpdate()
    {
        App::getComponent('cache')->clear();
    }
}

