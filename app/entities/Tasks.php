<?php

namespace app\entities;

use app\App;
use app\Entity;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="tasks", indexes={@Index(name="user_id", columns={"user_id"})})
 */
class Tasks extends Entity
{
    public static $createRules = [
        'required'=> [['user_id', 'text', 'csrf']],
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
            $user = $usersRepository->findOneBy(['id' => $data['user_id']]);
            if (!$user) {
                return false;
            }
            $data['user'] = $user;
            $task = static::create($data);
            return $task;
        } catch (\Exception $e) {
            if (App::getConfig('app.debug')) {
                d($e);
            }
        }
    }


    /**
     * @Column(type="text", name="text", length=65535, nullable=false )
     */
    private $text;

    /**
     *  @Column(type="boolean", name="status", nullable=false, options={"default":0})
     */
    private $status = '0';

    /**
     *  @Column(type="datetime", name="createdAt", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @Column(type="datetime", name="updatedAt", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * @Column(type="integer", name="id")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="app\entities\Users")
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
     * @PreUpdate
     */
    public function doPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
    /**
     * @PostUpdate
     * @PostPersist
     */
    public function doPostUpdate()
    {
        App::getComponent('cache')->clear();
    }
}

