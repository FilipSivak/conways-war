goog.provide("wa1.controllers.GameBoard");

goog.require("wa1.components.Board");
goog.require("wa1.util.JsonRequest");
goog.require("wa1.util.Binder");

goog.require("goog.Uri.QueryData");
goog.require("goog.ui.Dialog");
goog.require("goog.Timer");

/**
	@construct
*/
wa1.controllers.GameBoard = function() {
	// TODO: hardcoded constant
	this.canPut = 20;
	
	// TODO: automaticaly callculate (because of long games ??)
	this.animationStep = 500;	// in ms
};

wa1.controllers.GameBoard.prototype.play = function(gameId) {
	var request = new wa1.util.JsonRequest();
	
	// prepare request data
	var data = new goog.Uri.QueryData();
	data.add("id", gameId);
	
	this.player = undefined;
	this.opponent = undefined;
	
	var that = this;
	request.send( "gamePlay/play", "POST", data, function(json) {		
		var loggedUser = wa1.controllers.Auth.loggedUser;
		var enemy = undefined;
		
		that.player = loggedUser;
		that.opponent = that.getEnemyForPlayer( that.player.id, json.game );
		console.log( that.opponent );
		
		// insert player infoboxes
		var html =  wa1.templates.game.gameBoard({player: that.player, opponent: that.opponent});
		var el = goog.dom.getElement("content");
		el.innerHTML = html;
		
		that.json = json;
		that.canPut = that.json.remainLifePlayer;
		that.canPutEnemy = that.json.remainingLifeEnemy;
		
		var canPutPlayer = goog.dom.getElement("canPut_player");
		var canPutOpponent = goog.dom.getElement("canPut_opponent");
		
		canPutPlayer.innerHTML = that.canPut;
		canPutOpponent.innerHTML = that.canPutEnemy;
		
		// game board
		var board = new wa1.components.Board( "board", loggedUser.id, that.opponent.id, 800, 400 );
		
		var timer = new goog.Timer( that.animationStep );
		var i = 0;
		var iMax = json.moves.length;
		goog.events.listen(timer, goog.Timer.TICK, function() {
			console.log( "tick" );
			if( i < iMax ) {
				board.executeMoves( json.moves[i] );
				i++;
				
				// TODO: constant 
				// TODO: show steps
				for(var y=0; y < 5; y++) {
					board.step();	
				} 
			}else {
				this.stop();
				
				// Animation done, continue
				// score
				var scorePlayer = goog.dom.getElement("onBoard_player");
				var onBoardPlayer = board.getLifeOnBoardCount( that.player.id );
				scorePlayer.innerHTML = onBoardPlayer;
				
				var scoreOpponent = goog.dom.getElement("onBoard_opponent");
				var onBoardOpponent = board.getLifeOnBoardCount( that.opponent.id );
				scoreOpponent.innerHTML = onBoardOpponent;
				
				// check if game is over
				if((that.canPut + that.canPutEnemy) == 0) {
					that.finished = 1;
					if(onBoardPlayer > onBoardOpponent) {
						winner = that.player;
					}else if(onBoardPlayer < onBoardOpponent) {
						winner = that.opponent;
					}else {
						winner = 1;	// 1 stands for DRAW
					}
				
					wa1.controllers.GameBoard.endGame( that.json.game.id, winner, onBoardPlayer, onBoardOpponent );
				}
				
				// TODO: is direct indexing evil?
				var btnSubmit = goog.dom.getElementsByTagNameAndClass("button", "btnSubmitMove")[0];
				btnSubmit.enabled = "false";
				
				that.json = json;
				// TODO: comment
				if(json.state == "WAIT") {
					btnSubmit.innerText = "Waiting for opponent's move (Replay and refresh)";
					
					var timer = new goog.Timer( 1000 );
					goog.events.listen(timer, goog.Timer.TICK, function(ev) {
						var el = goog.dom.getElement( "gameBoard" );
						console.log( ev );
						if(el == null) {
							ev.target.stop();
						}
						
						var request = new wa1.util.JsonRequest();
						var data = new goog.Uri.QueryData();
						data.add("id", gameId);
						
						request.send("gamePlay\\isReady", "POST", data, function(json){
							if(json.status == "OK") {
								that.play( that.json.game.id );
								ev.target.stop();
							}
						});
					});
					
					if(that.finished != 1) {
						timer.start();
					}
					
					goog.events.listen(btnSubmit, goog.events.EventType.CLICK, function(event) {
						that.play( that.json.game.id );
					});
					return;
				}
				
				// register onPut callback - called every time player put's new life cell on the board
				board.onPut = function() {
					that.canPut--;
					if(that.canPut == 0) board.locked = true;
					canPutPlayer.innerHTML = that.canPut;
					btnSubmit.enabled = "true";
				};
				
				wa1.util.Binder.bind("button", "btnSubmitMove", "content", function(event) {
					if(that.canPut == 20) {
						alert("You must place at least one additional life cell on the board, before ending the turn!");
						return;
					}
					
					var request = new wa1.util.JsonRequest();
					
					// TODO: cannot be resent!
					var moves = board.popMoves();
					var data = new goog.Uri.QueryData();
					data.add("moves", goog.json.serialize(moves));
					data.add("gameId", gameId);
					
					request.send("gamePlay/move", "POST", data, function(json) {
						that.play( that.json.game.id );
						return;
					});
				});
			}
		});
		timer.start();
		
		/*goog.array.forEach(json.moves, function(move) {
			board.executeMoves( move );
			
			var timer = new 
			
			// TODO: nice animation
			// TODO: hardcoded constants
			for(var i = 0; i < 10; i++) {
				board.step();
			}
		});*/
		

		
	});
};

wa1.controllers.GameBoard.endGame = function(gameId, winner, playerScore, opponentScore) {
	el = goog.dom.getElement( "content" );
	var html = wa1.templates.game.endGame({winner: winner, playerScore: playerScore, opponentScore: opponentScore});
	
	var dialog = new goog.ui.Dialog();
	dialog.setContent( html );
	dialog.setVisible( true );
	
	var request = new wa1.util.JsonRequest();
	
	var data = new goog.Uri.QueryData();
	data.add("gameId", gameId);
	
	request.send("game/finish", "POST", data, function(json) {
		// just send, don't do anything with response
	});
};

wa1.controllers.GameBoard.prototype.getEnemyForPlayer = function(playerId, game) {	
	if(playerId == game.owner.id) {
		enemy = game.opponent;
	}else if(playerId == game.opponent.id) {
		enemy = game.owner;
	}else {
		throw "No player found. Weird ..";
	}
	
	return enemy;
};