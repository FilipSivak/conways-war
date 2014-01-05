<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 1.1.14
 * Time: 18:15
 */

namespace util;

// TODO: all other exception in util should probably extend this
/** Exception that is handled by client */
class AppException extends \RuntimeException {

    public function __construct($message = null) {
        parent::__construct($message);
    }

} 