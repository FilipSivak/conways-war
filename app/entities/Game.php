<?php

namespace entities;
use Symfony\Component\Yaml\Exception\RuntimeException;

/** Represents game, that was created by Player $owner.
 *  @Entity(repositoryClass="repositories\GameRepository")
    @Table(name="game")
 */
class Game {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	public $id;
	
	/**
     * Denotes, whether game is visible.
     * Hidden game is currently unplayable.
     * @Column(type="boolean") */
	protected $isPublic;
	
	 /**
	 * Link to game, so that other player can just type the link and join.
	 * Link is automatically generated (by application, not database)
      * NOT YET IMPLEMENTED
	 * @Column(type="string") */
	protected $link;
	
	/** 
	 * Name of game
	 * @Column(type="string")*/
	protected $name;
	
	/** 
	 *	State of game (running, finished, timeouted) 
	 * @Column(type="integer")
	 */
	protected $state;
	 
	/** 
	 * DateTime of start of game (the time, when other player joined)
	 * 
	 * @Column(type="datetime", nullable=true)
	 * */
	protected $gameStartTime;

    /** DateTime of creation of game (the time, when game was created)
     * @Column(type="datetime")  */
    protected $gameCreateTime;

    /**
     * The one, who created game.
     * @ManyToOne(targetEntity="entities\Player")
     *  @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /** The one, who have joined game.
     *  @ManyToOne(targetEntity="entities\Player")
        @JoinColumn(name="opponent_id", referencedColumnName="id")
     */
    protected $opponent;

    /** json conversion (converted into json later on ..)
     *  @return array */
    public function toArray() {
        $data = array(
            "id" => $this->getId(),
            "title" => $this->getName(),
            "link" => $this->getLink(),
            "public" => $this->isPublic(),
            "state" => $this->getState()
        );

        if($this->getGameStartTime() != null) {
            $data["gameStartTime"] = $this->getGameStartTime()->format("j/m/Y H:i");
        }

        if($this->getGameCreateTime() != null) {
            $data["gameCreateTime"] = $this->getGameCreateTime()->format("j/m/Y H:i");
        }

        if($this->getOwner() != null) {
            $data["owner"] = $this->getOwner()->toArray();
        }

        if($this->getOpponent() != null) {
            $data["opponent"] = $this->getOpponent()->toArray();
        }

        return $data;
    }

    /**
        Gets enemy for given player.
     *  If player is owner, returns opponent, and vice versa.
     */
    public function getEnemyFor($playerId) {
        if($playerId == $this->getOwner()->getId()) {
            $enemy = $this->getOpponent();
        }else if($playerId == $this->getOpponent()->getId()) {
            $enemy = $this->getOwner();
        }else {
            throw new RuntimeException("Illegal state!");
        }

        if($enemy->getId() == $playerId) throw new \RuntimeException( "WTF?!" );
        return $enemy;
    }

	public function getId() { return $this->id; }
	
	public function getLink() { return $this->link; }
	public function setLink( $link ) {  $this->link = $link; }
	
	public function setPublic($isPublic) { $this->isPublic = $isPublic; }
	public function isPublic() { return $this->isPublic == true; }

    /**
     * @param mixed $owner
     */
    public function setOwner(Player $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $opponent
     */
    public function setOpponent($opponent)
    {
        if($opponent == $this->owner) {
            throw new RuntimeException("Opponent cannot be same as owner!");
        }
        $this->opponent = $opponent;
    }

    /**
     * @return mixed
     */
    public function getOpponent()
    {
        return $this->opponent;
    }



    /**
     * @param mixed $gameStartTime
     */
    public function setGameStartTime($gameStartTime)
    {
        $this->gameStartTime = $gameStartTime;
    }

    /**
     * @return mixed
     */
    public function getGameStartTime()
    {
        return $this->gameStartTime;
    }

    /**
     * @param mixed $gameCreateTime
     */
    public function setGameCreateTime($gameCreateTime)
    {
        $this->gameCreateTime = $gameCreateTime;
    }

    /**
     * @return mixed
     */
    public function getGameCreateTime()
    {
        return $this->gameCreateTime;
    }



    /**
     * @param mixed $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }
	
	public function getName() { return $this->name; }
	
	public function setName($name) {
		$this->name = $name;
	}
	
}

?>