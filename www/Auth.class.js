/** 
	@fileoverview Handles login and registration
*/

goog.provide("wa1.controllers.Auth");

goog.require("wa1.templates.auth");
goog.require("wa1.util.JsonRequest");

/** 
	@constructor
*/
wa1.controllers.Auth = function() {	
};

wa1.controllers.Auth.loggedUser = undefined;

wa1.controllers.Auth.loginForm = function(el) {
		
	var el = goog.dom.getElement( el );
	if(el == undefined) throw "Insertion element not found!";
		
	var html = wa1.templates.auth.login();
	el.innerHTML = html;
	
	var form = new wa1.util.Form("formLogin", function(json){
		if(json.auth == 1) {
			wa1.controllers.Auth.onLoggedIn( json.player );
		}else if(json.auth == 0) {
			// login failed
			wa1.util.ErrorHandler.ShowError("Wrong email or password!");
		}else {
			console.log( json );
			throw "Unknown message: '"+json+"'";
		}
	});
};

wa1.controllers.Auth.registerForm = function(el) {
	var el = goog.dom.getElement( el );
	if(el == undefined) throw "Insertion element not found!";
	
	var html = wa1.templates.auth.register();
	el.innerHTML = html;
	
	var form = new wa1.util.Form("formRegister", function(response) {
		response = goog.json.parse( response );
		wa1.controllers.Auth.onLoggedIn( response.player );
	});
};

wa1.controllers.Auth.onLoggedIn = function(user) {
	console.log("Logged in: " + user);
	var loginInfo = goog.dom.getElement("loginInfo");
	loginInfo.innerText = "Logged in as: " + user.email;
	
	// add logout option
	var loginBar = goog.dom.getElement("loginBar");
	// TODO: closure tools equvivalent?
	loginBar.innerHTML = "<li><a href='#logout' id='doLogOut'>Log out</a></li>" + loginBar.innerHTML;
	
	wa1.controllers.Auth.loggedUser = user;
	
	var aLogOut = goog.dom.getElement("doLogOut");
	goog.events.listen(aLogOut, goog.events.EventType.CLICK, function(e){
		goog.events.Event.preventDefault(e);
		
		var jsonRequest = new wa1.util.JsonRequest();
		jsonRequest.send("auth/logout", "GET",undefined, function(json){
			if(json.isOk != 1) {
				alert('Could not log off!');
			}else {
				wa1.controllers.Auth.onLogout();
			}
		});
		
	});
	
	wa1.controllers.Game.listGames( "content" );
};

wa1.controllers.Auth.onLogout = function() {
	var loginInfo = goog.dom.getElement("loginInfo");
	loginInfo.innerHTML = "Not logged in.";
	
	wa1.controllers.Auth.loggedUser = undefined;
	
	wa1.controllers.Auth.loginForm( "content" );
};