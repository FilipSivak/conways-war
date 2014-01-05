<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 30.12.13
 * Time: 14:53
 */

namespace util;

/** thrown in registration process, when user with email adress is present in database */
class UserAlreadyExistsException extends AppException {

    public function __construct($message) {
        parent::__construct($message);
    }

} 