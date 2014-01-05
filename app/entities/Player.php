<?php

namespace entities;

use \Nette\Security\IIdentity;

/** Abstract player. Parent of players.
 *
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"registered" = "RegisteredPlayer", "bot" = "Bot"})
 * @Table(name="player")
 */
class Player implements IIdentity {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	
	/**
     *  Serves for login.
     * @Column(type="string") */
	protected $email;
	
	/** Serves for game recognition between players.
     * @Column(type="string") */
	protected $nickname;

    /**
     * Returns a list of roles that the user is a member of.
     * NOT IMPLEMENTED
     * @return array
     */
    function getRoles()
    {
        // TODO: implement roles if needed (maybe admin role)
        return array();
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /** Method is used for export to json via JsonResponse */
    public function toArray() {
        return array(
            "id" => $this->getId(),
            "email" => $this->getEmail(),
            "nickname" => $this->getNickname()
        );
    }

}

?>