<?php

namespace repositories;


class RegisteredPlayerRepository extends BaseRepository {

    public function findByLogin($login) {

        $query = $this->getEntityManager()->createQuery('SELECT p FROM entities\\RegisteredPlayer p WHERE p.login = :login');
        $query->setParameter("login", $login);
        return $query->getSingleResult();

    }

} 