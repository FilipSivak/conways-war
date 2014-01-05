<?php
/**
 * Created by PhpStorm.
 * User: You shall not pass
 * Date: 2.1.14
 * Time: 19:41
 */

use \Nette\Application\Responses\JsonResponse;
use \entities\GameMove;
use \entities\Game;
use \entities\CellChanged;
use util\JsonException;

/**
    Presenter handles gameplay
 */
class GamePlayPresenter extends BasePresenter
{

    /**
     *  Handles game start (game play)
     *
     * Responds with moves.
     * Check's who is next to be sending move.
     */
    public function renderPlay()
    {
        try {
            $gameId = $this->getHttpRequest()->getPost("id");
            $playerId = $this->getUser()->getIdentity()->getId();

            /** @var entities\Game $game */
            $game = $this->em->find("entities\\Game", $gameId);

            if ($game == null) {
                throw new AppException("Game with id '" . $this->get . "' not found!");
            }

            /** @var repositories\CellChangedRepository $cellChangedRepo */
            $cellChangedRepo = $this->em->getRepository("entities\\CellChanged");

            /** @var entities\GameMove $gameMove */
            $cells = $cellChangedRepo->findCellsByGame($gameId);

            $json = array();
            foreach ($cells as $cell) {
                /** @var entities\CellChanged $cell */
                $cell = $cell;

                // group by round
                $json[$cell->getGameMove()->getRound()][] = $cell->toArray();
            }

            $jsonMoves = array();
            foreach ($json as $move) {
                $jsonMoves[] = $move;
            }

            // TODO: Performance: pass instane of game instead?
            /** @var entities\Player $nextPlayer */
            $nextPlayer = $this->em->getRepository("entities\\GameMove")->whosNext($gameId);
            if ($nextPlayer->getId() == $playerId) {
                $state = "OK";
            } else {
                $state = "WAIT";
            }

            // TODO: hardcoded constant hand life cells count
            $remainLifeCells = $this->em->getRepository("entities\\GameMove")->getLifeCellsCount($gameId, $playerId);
            $remainingPlayer = 20 - $remainLifeCells;

            $remainLifeCells = $this->em->getRepository("entities\\GameMove")->getLifeCellsCount($gameId, $game->getEnemyFor($playerId)->getId());
            $remainingEnemy = 20 - $remainLifeCells;

            // if player has no life cells on hand, he can only wait
            if ($remainingPlayer == 0) {
                $state = "WAIT";
            }

            $this->sendResponse(new JsonResponse(array(
                "game" => $game->toArray(),
                "moves" => $jsonMoves,
                "state" => $state,
                "remainLifePlayer" => $remainingPlayer,
                "remainingLifeEnemy" => $remainingEnemy
            )));
        } catch (AppException $ae) {
            $this->sendResponse(new JsonException($ae));
        }
    }

    /**
     *  Stat's bot play and immediately sends first bot's move.
     */
    public function renderBotPlay() {
        $playerId = $this->getUser()->getId();
        $playerRef = $this->em->getReference("entities\\Player", $playerId);

        // TODO: make selection form
        $botId = 9;
        $botRef = $this->em->getReference("entities\\Bot", $botId);
        if($botRef == null) {
            throw new RuntimeException("Bot is null!");
        }

        $game = new Game();
        $game->setState( 0 );
        $game->setGameCreateTime( new DateTime("now") );
        $game->setGameStartTime( new DateTime("now") );
        $game->setIsPublic( false );
        $game->setOwner( $playerRef );
        $game->setOpponent( $botRef );
        $game->setName( "Bot play" );   // TODO: name from form
        $game->setLink( md5(rand(4816,6816)) );

        $this->em->persist( $game );
        $this->em->flush();

        $this->botInsertMove($game->getId(), $botId, 1);

        $this->sendResponse( new JsonResponse(array(
            "isOk" => 1,
            "gameId" => $game->getId()
        )));
    }

    /** Insert's R-Pentonimo structure of life cells */
    protected function botInsertMove($gameId, $botId, $round) {
        /** @var entities\Game $gameRef */
        $gameRef = $this->em->getReference("entities\\Game", $gameId);
        $botRef = $this->em->getReference("entities\\Player", $botId);

        // check whether bot can continue play
        $remaining = 20 - $this->em->getRepository("entities\\GameMove")->getLifeCellsCount($gameId, $botId);
        if($remaining < 5) return;

        $gameMove = new GameMove();

        $baseX = rand(0, 800 / 20 - 3);
        $baseY = rand(0, 400 / 20 - 3);
        self::addBotCells($gameMove, $baseX, $baseY, array(
            "0,1",
            "1,0",
            "1,1",
            "1,2",
            "2,0"
        ));

        $gameMove->setGame( $gameRef );
        $gameMove->setPlayer( $botRef );
        $gameMove->setSubmitTime( new DateTime("now") );
        $gameMove->setRound( $round );   // TODO: calc round? Maybe remove round as it's pointless ..

        $this->em->persist( $gameMove );
        $this->em->flush();
    }

    /** Adds cells given by array of "x,y" strings into gameMove */
    protected function addBotCells(entities\GameMove $gameMove,$baseX,$baseY, $points)
    {
        foreach ($points as $p) {
            $xy = explode(",", $p);
            $x = $xy[0];
            $y = $xy[1];

            $cell = new CellChanged();
            $cell->setCoordX($baseX + $x);
            $cell->setCoordY($baseY + $y);
            $cell->setMoveType("put");
            $cell->setGameMove( $gameMove );

            $gameMove->getCells()->add( $cell );
        }

        return $gameMove;
    }

    /**
     *  Get's move from json request and persist's it
     */
    public function renderMove()
    {
        // TODO: secure players
        $this->securedMethod();

        $request = $this->getHttpRequest();
        $data = $request->getPost("moves");
        $gameId = $request->getPost("gameId");

        $cells = json_decode($data);
        $gameRef = $this->em->getReference("entities\\Game", $gameId);
        $playerRef = $this->em->getReference("entities\\Player", $this->getUser()->getIdentity()->getId());

        $lastMove = $this->em->getRepository("entities\\GameMove")->getLastMove($gameId, $this->getUser()->getId());
        if (empty($lastMove)) {
            $round = 1;
        } else {
            $round = $lastMove[0]->getRound() + 1;
        }

        $move = new GameMove();
        $move->setRound($round);
        $move->setSubmitTime(new \Nette\DateTime("now"));
        $move->setGame($gameRef);
        $move->setPlayer($playerRef);
        $this->em->persist($move);
        $this->em->flush();

        foreach ($cells as $cell) {
            $cellChanged = new CellChanged();
            $cellChanged->setCoordX($cell->x);
            $cellChanged->setCoordY($cell->y);
            $cellChanged->setMoveType("put"); // TODO: pointless
            $cellChanged->setGameMove($move);

            $this->em->persist($cellChanged);
        }
        $this->em->flush();

        // TODO: check via discriminator column
        if($gameRef->getOpponent()->getId() == 9) {
            $this->botInsertMove($gameId, $gameRef->getOpponent()->getId(), $round + 1);
        }

        $this->sendResponse(new JsonResponse(array("ok" => "1")));
    }

    /** Check's whether move of opponent was submitted */
    public function renderIsReady()
    {
        $this->securedMethod();

        $gameId = $this->getHttpRequest()->getPost("id");
        $playerId = $this->getUser()->getId();

        $nextPlayer = $this->em->getRepository("entities\\GameMove")->whosNext($gameId);

        if ($playerId == $nextPlayer->getId()) {
            $status = "OK";
        } else {
            $status = "WAIT";
        }

        $this->sendResponse(new JsonResponse(array(
            "status" => $status
        )));
    }
} 