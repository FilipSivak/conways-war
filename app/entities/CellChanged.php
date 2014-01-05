<?php

namespace entities;

/**
 *  Represents single life cell that was added to board.
 *  Is child of GameMove.
 * @Entity(repositoryClass="repositories\CellChangedRepository")
    @Table(name="cellchanged")
 */
class CellChanged {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	
	/** @Column(type="integer") */
	protected $coordX;
	
	/** @Column(type="integer") */
	protected $coordY;
	
	/** Not used.
     * @Column(type="integer") */
	protected $moveType;

    /** @ManyToOne(targetEntity="entities\GameMove")
    @JoinColumn(name="gameMove_id", referencedColumnName="id", onDelete="CASCADE") */
    protected $gameMove;

    /**
        Function for json conversion (converts to array, that is further converted into json by Nette Framework)
     *  @return array
     */
    public function toArray() {
        return array(
            "x" => $this->getCoordX(),
            "y" => $this->getCoordY(),
            "moveType" => "put",    // TODO: what to do with this?
            "move" => "put",    // TODO: and this?
            "player" => $this->getGameMove()->getPlayer()->getId()
        );
    }

    /**
     * @param mixed $coordX
     */
    public function setCoordX($coordX)
    {
        $this->coordX = $coordX;
    }

    /**
     * @return mixed
     */
    public function getCoordX()
    {
        return $this->coordX;
    }

    /**
     * @param mixed $coordY
     */
    public function setCoordY($coordY)
    {
        $this->coordY = $coordY;
    }

    /**
     * @return mixed
     */
    public function getCoordY()
    {
        return $this->coordY;
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
     * @param mixed $moveType
     */
    public function setMoveType($moveType)
    {
        $this->moveType = $moveType;
    }

    /**
     * @return mixed
     */
    public function getMoveType()
    {
        return $this->moveType;
    }

    /**
     * @param mixed $gameMove
     */
    public function setGameMove($gameMove)
    {
        $this->gameMove = $gameMove;
    }

    /**
     * @return mixed
     */
    public function getGameMove()
    {
        return $this->gameMove;
    }
	
}

?>