<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 2.1.14
 * Time: 21:39
 */

namespace repositories;

/** Repository for CellChanged entity */
class CellChangedRepository extends BaseRepository {

    /** Find's all cells by game, fetches gameMove, and player */
    public function findCellsByGame($gameId) {

        $gameRef = $this->_em->getReference("entities\\Game", $gameId);

        /** @var \Doctrine\ORM\Query */
        $query = $this->_em->createQuery("  SELECT c,gm,p
                                            FROM entities\\CellChanged c
                                            JOIN c.gameMove gm
                                            JOIN gm.player p
                                            WHERE gm.game = :game
                                            ORDER BY gm.round ASC");
        $query->setParameter("game", $gameRef);

        // TODO: hydratation ?
        return $query->getResult();
    }

} 