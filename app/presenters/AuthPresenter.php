<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 29.12.13
 * Time: 23:56
 */

use Nette\Application\Responses\JsonResponse;
use Nette\Security\IAuthenticator;
use entities\RegisteredPlayer;
use util\JsonException;
use util\AuthException;
use util\UserAlreadyExistsException;

class AuthPresenter extends BasePresenter implements IAuthenticator  {

    const MSG_CREDENTIALS_WRONG = "Wrong login or password!";

    public function startup() {
        parent::startup();
        $this->getUser()->setAuthenticator( $this );
    }

    public function renderLogin() {
        // get parameters from request
        try {
            $r = $this->getHttpRequest();
            $email = $r->getPost('email');
            $password = $r->getPost('password');

            // get user service
            $user = $this->getUser();

            if($user->isLoggedIn()) {
                $this->sendResponse( new JsonResponse( array(
                    "player" => $user->getIdentity()->toArray(),
                    "alreadyLoggedIn" => true
                )));
            }

            $user->login($email, $password);

            // user login is not limited by time, user will be logged out after closing browser
            $user->setExpiration(0, true);

            if($user->isLoggedIn()) {
                $this->sendResponse(new JsonResponse(array(
                    "auth" => 1,
                    "player" => $user->getIdentity()->toArray()
                )));
            }
        }catch(AuthException $ex) {
            $this->sendResponse(new JsonResponse(array(
                "auth" => 0
            )));
        }catch(Exception $e) {
            throw $e;
        }
        // TODO: handle AppException?
    }

    public function renderGetIdentity() {
        $user = $this->getUser();

        if($user->isLoggedIn()) {
            $this->sendResponse( new JsonResponse( array("player" => $user->getIdentity()->toArray()) ) );
        }else {
           $this->sendResponse( new JsonException( new AuthException() ) );
        }
    }

    public function renderLogout() {
        $user = $this->getUser();
        $user->logout();

        $this->sendResponse( new JsonResponse(array(
            "isOk" => 1
        )));
    }

    public function renderRegister() {
        $r = $this->getHttpRequest();
        $email = $r->getPost('email');
        $nickname = $r->getPost("nickname");
        $password = $r->getPost('password');

        // TODO: server side validation

        try {
            $playerRepo = $this->em->getRepository("entities\\Player");
            $player = $playerRepo->findByEmail($email);

            // if user already exists
            if($player != null) {
                throw new UserAlreadyExistsException("User with email '".$email."' already exists!");
            }else {
                $player = new RegisteredPlayer();
                $player->setNickname( $nickname );
                $player->setEmail( $email );
                $salt = rand(4815,1623);
                $player->setSalt( $salt );
                $player->setPassword( $this->hash($password, $salt) );

                $this->em->persist($player);
                $this->em->flush();

                // login user right away
                $this->getUser()->login( $player->getEmail(), $player->getPassword() );

                $this->sendResponse( new JsonResponse($player->toArray()));
            }
        }catch(UserAlreadyExistsException $e) {
            $this->sendResponse( new JsonException($e) );
        }catch(\util\AppException $ae) {
            $this->sendResponse( new JsonException($ae) );
        }
    }

    public function authenticate(array $credentials) {
        $email = $credentials[0];
        $password = $credentials[1];

        /** @var  $playerRepo */
        $playerRepo = $this->em->getRepository("entities\\RegisteredPlayer");
        /** @var entities\Player */
        $player = $playerRepo->findOneByEmail($email);

        // player does not exist
        if($player == null) throw new AuthException( self::MSG_CREDENTIALS_WRONG );

        if($player->getPassword() === $this->hash($password, $player->getSalt())) {
            // TODO: roles yet to be defined!
            return $player;
        }else {
            throw new AuthException( self::MSG_CREDENTIALS_WRONG );
        }
    }

    protected function hash($password, $salt) {
        // TODO: use crypt
        return sha1($password.$salt."baf".$salt);
    }

}