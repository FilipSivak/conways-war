/** 
	@fileoverview Class Game - model of game
*/
goog.provide('wa1.controllers.Game');

goog.require("goog.Timer");
goog.require("goog.Uri.QueryData");

goog.require("wa1.templates.game");
goog.require('wa1.config');
goog.require('wa1.util.ErrorHandler');
goog.require("wa1.util.Form");
goog.require("wa1.util.Binder");
goog.require("wa1.controllers.GameBoard");

/** 
	@constructor
*/
wa1.controllers.Game = function(el)  {
	this.component = goog.ui.Component();
};

wa1.controllers.Game.REFRESH_INTERVAL = 2000;	// in ms

wa1.controllers.Game.newGame = function(el) {
	
	el = goog.dom.getElement( el );
	
	var html = wa1.templates.game.newGame();
	el.innerHTML = html;
	
	var game = undefined;
	
	// form binding
	var form = new wa1.util.Form("formNewGame", function(response){
		game = response;
		
		wa1.controllers.Game.listGames( "content" );
	});
	
};

wa1.controllers.Game.listGames = function(el, pageNumber) {
	if(pageNumber == undefined) var pageNumber = 1;
	
	var el = goog.dom.getElement( el );
	
	var data = new goog.Uri.QueryData();
	data.add("page", pageNumber);
	
	var request = new wa1.util.JsonRequest();
	request.send("game/listGames", "POST", data, function(json){
		var user =  wa1.controllers.Auth.loggedUser;
		
		var headline = ["Title","Owner","Created at", "Action"];
		var playerId = user.id != undefined ? user.id : 0;
		
		var template = wa1.templates.game.dataTable( {
			scopeId: "dataTable", 
			header: headline, 
			data: json.result, 
			playerId: playerId,
			pagerId: "pager",
			currentPage: json.page,
			maxPage: json.pages
		});
		el.innerHTML = template;
		
		// bind button actions
		wa1.util.Binder.bind( "button", "btnCancell", "dataTable", function(e) {
			var btn = e.target;
			var id = btn.getAttribute("data-id");
			wa1.controllers.Game.doCancel( id );
		});
		
		wa1.util.Binder.bind("button", "btnJoinGame", "dataTable", function(e) {
			var btn = e.target;
			var id = btn.getAttribute("data-id");
			wa1.controllers.Game.join( id );
		});
		
		wa1.util.Binder.bind("button", "btnPlay", "dataTable", function(e) {
			var btn = e.target;
			var gameId = btn.getAttribute("data-id");
			
			wa1.controllers.Game.play( gameId );
		});
		
		// bind pager buttons
		wa1.util.Binder.bind("a", "pageBack", "pager", function(ev) {
			wa1.controllers.Game.listGames(el, pageNumber - 1);
		});
		
		wa1.util.Binder.bind("a", "pageForward", "pager", function(ev) {
			wa1.controllers.Game.listGames(el, pageNumber + 1);
		});
		
		wa1.util.Binder.bind("a", "btnPage", "pager", function(ev) {
			var btn = ev.target;
			var pageNumber = btn.getAttribute("data-page");
			wa1.controllers.Game.listGames(el, pageNumber);
		});
	});
	
};

wa1.controllers.Game.doCancel = function(id) {
	var request = new wa1.util.JsonRequest();
	var data = new goog.Uri.QueryData();
	data.add("id", id);
	
	request.send("game/cancel", "POST", data, function(json){
		if(json.isOk == 1) {
			wa1.controllers.Game.listGames( "content" );
			// TODO: what game?
			wa1.util.ErrorHandler.ShowSuccess( "Game successfully cancelled!" );
		}else {
			wa1.util.ErrorHandler.ShowError("Game could not be cancelled!");
		}
		
	});
};

wa1.controllers.Game.join = function(gameId) {
	var request = new wa1.util.JsonRequest();
	var data = new goog.Uri.QueryData();
	data.add("id", gameId);
	
	request.send( "game/join", "POST", data, function(json) {
		if(json.isOk == 1) {
			// start game
			wa1.controllers.Game.play( gameId );
		}else {
			wa1.util.ErrorHandler.ShowError("Could not join to game!");
		}
	});
};

wa1.controllers.Game.botGame = function() {
	var request = new wa1.util.JsonRequest();
	request.send("gamePlay/botPlay", "POST", undefined, function(json) {
		wa1.controllers.Game.play( json.gameId );
	});
};

wa1.controllers.Game.play = function(gameId) {
	var gameBoard = new wa1.controllers.GameBoard();
	gameBoard.play( gameId );
};