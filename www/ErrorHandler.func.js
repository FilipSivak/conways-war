goog.provide("wa1.util.ErrorHandler");

goog.require("wa1.controllers.Auth");

wa1.util.ErrorHandler.HandleXHR = function(e) {
	var xhr = e.target;
	
	// TODO: more verbose error message
	alert('Error: ' + xhr.getStatusText());
};

wa1.util.ErrorHandler.ExceptionHandler = function(exception) {	
	if(exception.className == "util\\AuthException") {
		console.log( exception.className );
		
		wa1.controllers.Auth.loginForm( goog.dom.getElement("content") );
	}else {
		alert("Error: " + exception.message);
		console.log( exception );
	}
};

wa1.util.ErrorHandler.ShowError = function(message) {
	alert("Error: " + message);
};

// TODO:  rename ErrorHandler to MessageHandler?
wa1.util.ErrorHandler.ShowSuccess = function(message) {
	alert("Success: " + message);
};