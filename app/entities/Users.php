<?php

namespace app\entities;

use app\Entity;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="users", uniqueConstraints={@UniqueConstraint(name="email", columns={"email"})})
 * @EntityListeners({"app\classes\listeners\UserListener"})
 */
class Users  extends Entity
{
    public static $rulesLogin = [
        'required' => [
            ['email', 'password', 'csrf']
        ],
        'email' => [
            ['email']
        ],

    ];
    public static $rulesRegister = [
        'required' => [
            ['email', 'password', 'first_name', 'last_name', 'csrf']
        ],
        'email' => [
            ['email']
        ],
        'equals' => [
            ['password', 'password_repeat']
        ],
        'lengthMin' => [
            ['password', 6], ['password_repeat', 6]
        ]

    ];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->password = md5(time());
    }

    /**
     * check permission
     * @param $permission
     * @return mixed
     */
    public function hasAccessTo($permission)
    {
        $permissions = json_decode($this->permissions, true);
        return !empty($permissions) && in_array($permission, $permissions);
    }

    /**
     * @Column(type="string", name="email")
     */
    private $email;

    /**
     * @Column(type="string", name="password")
     */
    private $password;

    /**
     * @Column(type="string", name="session_key", nullable=true)
     */
    private $sessionKey;

    /**
     * @Column(type="text", name="permissions", length=65535, nullable=true)
     */
    private $permissions;

    /**
     * @Column(type="datetime", name="last_login", nullable=true)
     */
    private $lastLogin;

    /**
     * @Column(type="string", name="first_name", nullable=true)
     */
    private $firstName;

    /**
     * @Column(type="string", name="last_name", nullable=true)
     */
    private $lastName;

    /**
     * @Column(type="datetime", name="createdAt", options={"default":"CURRENT_TIMESTAMP"})
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
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function setSessionKey($sessionKey)
    {
        $this->sessionKey = $sessionKey;
        return $this;
    }

    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set permissions
     *
     * @param string $permissions
     *
     * @return Users
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return Users
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Users
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Users
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Users
     */
    public function setCreatedat($createdat)
    {
        $this->createdAt = $createdat;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedat()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedat
     *
     * @param \DateTime $updatedat
     *
     * @return Users
     */
    public function setUpdatedat($updatedat)
    {
        $this->updatedAt = $updatedat;

        return $this;
    }

    /**
     * Get updatedat
     *
     * @return \DateTime
     */
    public function getUpdatedat()
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
     * @PreUpdate
     */
    public function doPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}

