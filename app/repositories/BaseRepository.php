<?php

namespace repositories;

use Doctrine\ORM\EntityRepository;
use Nette\Reflection\ClassType;

/**
 * Description of BaseRepository
 *
 * @author Filip Sivak <sivakfil@fel.cvut.cz>
 */
abstract class BaseRepository extends EntityRepository {
    
    /** @var Nette\Reflection\ClassType */
    private $_reflection;
    
    public function persist(\Entities\Entity $e) {
		$this->_em->persist( $e );
    }
    
    // TODO: plug-in the reflection
    public function populateForm(\Nette\Application\UI\Form $form, \Entities\Entity $entity) {
		foreach($form->components as $componentName => $component) {
		    $key = "get".ucfirst($componentName);
		    
		    if( method_exists($entity, $key) && !is_null($entity->$key()) )
			$component->setValue( $entity->$key() );
		}
    }
    
    public function getEntityReflection() {
		if(is_null($this->_reflection)) {
		    $this->_reflection = new ClassType( $this->_entityName );
		}
		
		return $this->_reflection;
    }
    
    public function newEntityInstance() {
		$reflection = $this->getEntityReflection();
		return $reflection->newInstance();
    }
    
    /** 
     * @param EntityManager $em
     */
    public function getDataSource($alias = "e") {
		$em = $this->getEntityManager();
		
		$qb = $em->createQueryBuilder();
		$qb->select($alias)
		   ->from($this->_entityName, $alias);
		
		return $qb;
    }
    
    public function remove($id) {
		$em = $this->_em;
		$storageRef = $em->getReference($this->_entityName, $id);
		$em->remove($storageRef);
		$em->flush();
    }
    
}

?>
