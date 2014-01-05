<?php

use Nette\Application\Responses\JsonResponse;
use util\JsonException;
use Nette\Http\Request;

use entities\Game;

/**
 * Homepage presenter.
 */
class GamePresenter extends BasePresenter
{

    /** Marks presenter as secured */
    public function startup() {
        parent::startup();

        // All methods are secured
        $this->securedMethod();
    }

    /** Not used. */
	public function renderDefault()
	{
		// test of database
		$chatMessages = $this->em->getRepository("entities\ChatMessage")->findAll();
		
		foreach($chatMessages as $message) {
			echo "<h3>Bez entitites: '".$message->getText()."'</h3>";
			echo "<h3>S entities: '".htmlentities($message->getText())."'</h3>";
		}
		
		$this->template->chatMessages = $chatMessages;
	}
	
	/** Creates new game */
	public function renderNewGame() {
		$this->securedMethod();

		try {
			$request = $this->getHttpRequest();
		
			$game = new Game();
			$game->setName( $request->getPost('gameTitle') );
			$game->setPublic( $request->getPost('gamePublic') == "on" );
			$game->setLink( md5( rand(1000,9999) ) );
            $game->setState( 0 );   // TODO: named constant
            $game->setOwner( $this->em->getReference("entities\\Player", $this->getUser()->getId()) );
            $game->setGameCreateTime( new \DateTime("now") );

			$this->em->persist( $game );
			$this->em->flush();
			
			$this->sendResponse(new JsonResponse($game->toArray()));
			
		}catch(Exception $e) {
			// send exception to the client in case of error
			//$this->sendResponse( new JsonException($e) );
			throw $e;
		}
	}

    /** Return's paginated list of games */
    public function renderListGames() {
        $page = $this->getHttpRequest()->getPost("page");
        if($page == null) {
            $page = 1;
        }

        /** @var repositories\GameRepository $gameRepo */
        $gameRepo = $this->em->getRepository("entities\Game");

        /** @var \Doctrine\ORM\Query $gameListQuery */
        $gameListQuery = $gameRepo->getGameList( $this->getUser()->getIdentity()->getId(), $page );

        // Set pagination
        // TODO: introduce nice constants
        // TODO: move somewhere else!
        $MAX_RES = 8;
        $FIRST_RES = $MAX_RES * ($page - 1);

        $count = count( $gameListQuery->getResult() );  // TODO: is this ok?
        $gameListQuery->setMaxResults($MAX_RES);
        $gameListQuery->setFirstResult($FIRST_RES);

        // fetch result
        $gameList = $gameListQuery->getResult();

        $pages = ceil( $count / $MAX_RES );

        $result = array();
        foreach($gameList as $game) {
            $result[] = $game->toArray();
        }

        $this->sendResponse( new JsonResponse(array(
            "result" => $result,
            "pages" => $pages,
            "page" => $page
        )));
    }

    /** Joins game */
    public function renderJoin() {
        try {
            $this->securedMethod();

            $id = $this->getHttpRequest()->getPost("id");
            /** @var entities\Game $game */
            $game = $this->em->getRepository("entities\\Game")->find($id);

            // TODO: add database integrity constraint
            if($this->getUser()->getIdentity()->getId() == $game->getOwner()->getId()) {
                throw new AppException("You cannot join to game you have started!");
            }

            $opponentRef = $this->em->getReference("entities\\Player",$this->getUser()->getIdentity()->getId());
            $game->setOpponent( $opponentRef );
            $this->em->merge($game);
            $this->em->flush();

            $this->sendResponse(new JsonResponse(array("isOk" => 1)));
        }catch(AppException $ae) {
            $this->sendResponse( new JsonException($ae) );
        }
    }

    /** Called when game is over (finished) and sets Game->state to 1 (finished). */
    public function renderFinish() {
        $this->securedMethod();

        $gameId = $this->getHttpRequest()->getPost("gameId");
        /** @var entities\Game $game */
        $game = $this->em->find("entities\\Game", $gameId);

        // TODO: introduce constant
        $game->setState(1);
        $this->em->merge( $game );
        $this->em->flush();

        $this->sendResponse( new JsonResponse(array("ok" => 1)) );
    }

    /** Cancells game, taht player owns */
    public function renderCancel() {
        try {
            $id = $this->getHttpRequest()->getPost("id");

            // fetch game
            /** @var entities\Game $game */
            $game = $this->em->getRepository("entities\\Game")->find($id);

            // check whether game exists
            if($game == null) {
                throw new AppException("Game with id '".$id."' doesn't exist!");
            }

            // secure access to this game
            $this->securedToOwner( $game->getOwner()->getId() );

            // TODO: mark as cancelled instead of delete, maybe state?
            // delete game
            $this->em->remove( $game );
            $this->em->flush();

            $this->sendResponse(new JsonResponse( array("isOk" => 1) ));
        }catch(AppException $ae) {
            $this->sendResponse( new JsonException($ae) );
        }
    }

}

