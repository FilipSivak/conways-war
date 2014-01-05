<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 2.1.14
 * Time: 20:27
 */

namespace repositories;


use Symfony\Component\Yaml\Exception\RuntimeException;

class GameMoveRepository extends BaseRepository {

    public function findByPlayerAndGame($playerId, $gameId) {

        $playerRef = $this->_em->getReference("entities\\Player", $playerId);
        $gameRef = $this->_em->getReference("entities\\Game", $gameId);

        /** @var \Doctrine\ORM\Query */
        $query = $this->_em->createQuery("  SELECT gm
                                            FROM entities\\GameMove gm
                                            WHERE gm.game = :game AND gm.player = :player
                                            ORDER BY gm.round DESC");
        $query->setParameter("player", $playerRef);
        $query->setParameter("game", $gameRef);


        $query->setMaxResults(1);
        return $query->getSingleResult();
    }

    public function whosNext($gameId) {
        $gameRef = $this->_em->getReference( "entities\\Game", $gameId );
        $query = $this->_em->createQuery("  SELECT gm
                                            FROM entities\\GameMove gm
                                            JOIN gm.player p
                                            WHERE gm.game = :game
                                            ORDER BY gm.round DESC, gm.submitTime DESC");
        $query->setParameter("game", $gameRef);
        $query->setMaxResults( 1 );

        try {
            $res = $query->getSingleResult();
            $next = $gameRef->getEnemyFor( $res->getPlayer()->getId() );
        }catch(\Doctrine\ORM\NoResultException $nre) {
            $next = $gameRef->getOpponent();
        }

        // check if next has any remaining life on hand
        // TODO: default hand constant
        $remaining = 20 - $this->getLifeCellsCount($gameId,  $next->getId() );
        if($remaining == 0) {
            // choose opponent
            $next = $gameRef->getEnemyFor( $next->getId() );
        }

        return $next;
    }

    public function getLastMove($gameId, $playerId) {
        $qameRef = $this->_em->getReference("entities\\Game", $gameId);
        $playerRef = $this->_em->getReference("entities\\Player", $playerId);

        $query = $this->_em->createQuery("  SELECT gm
                                            FROM entities\\GameMove gm
                                            WHERE gm.player = :player AND gm.game = :game
                                            ORDER BY gm.round DESC");
        $query->setParameter("player", $playerRef);
        $query->setParameter("game", $qameRef);

        $query->setMaxResults(1);

        return $query->getResult();
    }

    public function getLifeCellsCount($gameId, $playerId) {
        $gameRef = $this->_em->getReference("entities\\Game", $gameId);
        $playerRef = $this->_em->getReference("entities\\Player", $playerId);

        $query = $this->_em->createQuery("  SELECT COUNT(c)
                                            FROM entities\\CellChanged c
                                            JOIN c.gameMove gm
                                            WHERE gm.game = :game AND gm.player = :player");
        $query->setParameter("player", $playerRef);
        $query->setParameter("game", $gameRef);

        $res = $query->getSingleResult();
        return $res[1];
    }

    public function findByMoveAndPlayer($moveId, $playerId) {
        /*$playerRef = $this->_em->getReference("entities\\Player", $playerId);
        $moveRef = $this->_em->getReference("entities\\GameMove", $moveId);

        /** var \Doctrine\ORM\Query $query */
        /*$query = $this->_em->createQuery("  SELECT gm
                                            FROM entities\\GameMove gm
                                            WHERE gm.g") */
    }

} 