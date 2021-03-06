<?php 

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."\\entities"), $isDevMode);

if(isset($_SERVER["HTTP_HOST"]) && ($_SERVER["HTTP_HOST"] == "dcgi67.felk.cvut.cz")) {
	$dbParams = parse_ini_file("config/dbconfig_prod.ini");
}else {
	$dbParams = parse_ini_file("config/dbconfig_test.ini");	
}

// obtaining the entity manager
return EntityManager::create($dbParams, $config);

?>