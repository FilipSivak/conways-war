<?php

namespace entities;

/** @Entity(repositoryClass="repositories\GameMoveRepository")
    @Table(name="gamemove")
 */
class GameMove {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	
	/** @Column(type="integer") */
	protected $round;
	
	/** @Column(type="datetime") */
	protected $submitTime;

    /** @ManyToOne(targetEntity="entities\Player")
        @JoinColumn(name="player_id", referencedColumnName="id") */
    protected $player;

    /** @ManyToOne(targetEntity="entities\Game")
    @JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE") */
    protected $game;

    /** @OneToMany(targetEntity="CellChanged", mappedBy="gameMove", cascade={"persist"})  */
    protected $cells;

    function __construct() {
        $this->cells = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param mixed $round
     */
    public function setRound($round)
    {
        $this->round = $round;
    }

    /**
     * @return mixed
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param mixed $submitTime
     */
    public function setSubmitTime($submitTime)
    {
        $this->submitTime = $submitTime;
    }

    /**
     * @return mixed
     */
    public function getSubmitTime()
    {
        return $this->submitTime;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param mixed $cells
     */
    public function setCells($cells)
    {
        $this->cells = $cells;
    }

    /**
     * @return mixed
     */
    public function getCells()
    {
        return $this->cells;
    }

}

?>