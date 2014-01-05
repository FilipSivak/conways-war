<?php

use util\JsonException;
use util\AuthException;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function startup() {
		parent::startup();

		$this->em = $this->getService("EntityManager");
    }
    
    public function isProduction() {
		return $this->context->params['productionMode'];
    }

    public function securedMethod() {
        if(!$this->getUser()->isLoggedIn()) {
            $this->authException("You must log in!");
        }
    }

    public function securedToOwner($userId) {
        $this->securedMethod();

        if($this->getUser()->getIdentity()->getId() != $userId) {
            $this->authException("You don't own the content!");
        }
    }

    private function authException($message) {
        $this->sendResponse(new JsonException(new AuthException()) );
    }
}

