<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 30.12.13
 * Time: 22:38
 */

namespace repositories;


class GameRepository extends BaseRepository {

    /** Returns QUERY of public games that can be joined to
     *  Query is intended to be paginated.
     */
    public function getGameList($playerId) {
        /** @var \Doctrine\ORM\Query */
        $query = $this->_em->createQuery("  SELECT g
                                            FROM entities\\Game g
                                            WHERE (g.isPublic = 1 AND g.opponent IS NULL)
                                            OR g.owner = :player
                                            OR g.opponent = :player
                                            ORDER BY g.gameCreateTime DESC");

        $playerRef = $this->_em->getReference("entities\\Player", $playerId);
        $query->setParameter("player", $playerRef);

        return $query;
    }

} 