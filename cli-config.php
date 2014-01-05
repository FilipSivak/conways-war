<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = require_once( "app/doctrine_bootstrap.php" );

/*$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));*/

return ConsoleRunner::createHelperSet($entityManager);

?>
