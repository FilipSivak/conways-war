<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 30.12.13
 * Time: 14:53
 */

namespace util;

class UserAlreadyExistsException extends \RuntimeException {

    public function __construct($message) {
        parent::__construct($message);
    }

} 