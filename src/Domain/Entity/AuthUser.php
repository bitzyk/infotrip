<?php

namespace Infotrip\Domain\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Events;

/**
 * Class AuthUser
 *
 * @ORM\Entity(repositoryClass="Infotrip\Domain\Repository\AuthUserRepository")
 * @ORM\Table(name="phpauth_users")
 *
 * @package Infotrip\Domain\Entity
 */
class AuthUser
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $email;

    /** @Column(type="integer") */
    private $isactive;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AuthUser
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return AuthUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * @param mixed $isactive
     * @return AuthUser
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;
        return $this;
    }
}