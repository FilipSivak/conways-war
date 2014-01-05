<?php

namespace entities;

/** @Entity(repositoryClass="repositories\RegisteredPlayerRepository")
 */
class RegisteredPlayer extends Player {
	
	/** @Column(type="string") */
	protected $password;
	
	/** @Column(type="string") */
	protected $salt;

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

}

?>