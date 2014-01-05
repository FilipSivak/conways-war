<?php

namespace entities;

/**
 *  Player that was registered by application.
 *  Other type of plyers will be for example FacebookPlayer
 * @Entity(repositoryClass="repositories\RegisteredPlayerRepository")
 */
class RegisteredPlayer extends Player {
	
	/** @Column(type="string") */
	protected $password;
	
	/**
     *  Random salt for each user. Salt is randomly generated number.
     * @Column(type="string") */
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