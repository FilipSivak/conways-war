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

    /** Setups EntityManger from DI container */
    public function startup() {
		parent::startup();

		$this->em = $this->getService("EntityManager");
    }

    /** Check's wheter evironment is production */
    public function isProduction() {
		return $this->context->params['productionMode'];
    }

    /** Throws \util\AuthException on not logged user  */
    public function securedMethod() {
        if(!$this->getUser()->isLoggedIn()) {
            $this->authException("You must log in!");
        }
    }

    /** Secures content to given userId - throws authException on different user beeing logged in */
    public function securedToOwner($userId) {
        $this->securedMethod();

        if($this->getUser()->getIdentity()->getId() != $userId) {
            $this->authException("You don't own the content!");
        }
    }

    /** Sends json AuthException */
    private function authException($message) {
        $this->sendResponse(new JsonException(new AuthException()) );
    }
}

