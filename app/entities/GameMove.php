<?php

namespace entities;

/** Single move in Game. Is child of Game.
 *  @Entity(repositoryClass="repositories\GameMoveRepository")
    @Table(name="gamemove")
 */
class GameMove {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	
	/** Specifies order of moves.
     * @Column(type="integer") */
	protected $round;
	
	/** DateTime when move was submitted.
     * @Column(type="datetime") */
	protected $submitTime;

    /** Player that has submitted move.
     *  @ManyToOne(targetEntity="entities\Player")
        @JoinColumn(name="player_id", referencedColumnName="id") */
    protected $player;

    /** Game this move belongs to.
     *  @ManyToOne(targetEntity="entities\Game")
    @JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE") */
    protected $game;

    /** Cells that are beeing putted in this move (childs).
     * @var \Doctrine\Common\Collections\ArrayCollection()
     * @OneToMany(targetEntity="CellChanged", mappedBy="gameMove", cascade={"persist"})  */
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