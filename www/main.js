goog.require("templates");

goog.require("wa1.components.Board");
goog.require("wa1.controllers.Game");
goog.require("wa1.controllers.Auth");
goog.require("wa1.util.Binder");

goog.require("goog.ui.BidiInput");
goog.require("goog.ui.Button");
goog.require("goog.ui.Container");
goog.require("goog.array");

var BOARD_WIDTH = 500;
var BOARD_HEIGHT = 200;

/** First starting function */
function start() {
	
	// binds listeners to HTML
	bindUI();
	
	// cals default action
	defaultAction();
}

/** 
 *  @param {string} name 
 * */
function bindUI()  {
	
	var bindScope = goog.dom.getElement("navigation");
	
	// bindings
	wa1.util.Binder.bind("a","gotoLogin",bindScope, function(e) {
		wa1.controllers.Auth.loginForm( "content" );
	});
	
	wa1.util.Binder.bind("a","gotoRegister",bindScope, function(e) {
		wa1.controllers.Auth.registerForm( "content" );
	});
		
	wa1.util.Binder.bind("a","gotoNewGame",bindScope, function(e) {
		wa1.controllers.Game.newGame( "content" );
	});
	
	wa1.util.Binder.bind("a","gotoBotGame",bindScope, function(e) {
		wa1.controllers.Game.botGame();
	});
	
	wa1.util.Binder.bind("a", "gotoListGames", bindScope, function(e) {
		wa1.controllers.Game.listGames("content");
	});
	
}

function defaultAction() {
	var request = new wa1.util.JsonRequest();
	request.send("auth/getIdentity", "GET", undefined, function(json){
		// SUCCESS
		wa1.controllers.Auth.onLoggedIn( json.player );
	},
	function(exception) {
		// FAIL
		wa1.controllers.Auth.onLogout();
		wa1.controllers.Auth.loginForm( "content" );
	});
};


start();