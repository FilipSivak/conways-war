goog.provide("wa1.util.JsonRequest");

goog.require("goog.net.XhrIo");

/** 
	@constructor
*/
wa1.util.JsonRequest = function() {

};

wa1.util.JsonRequest.prototype.send = function(url,method, data, successCallback, exceptionCallback) {
	var xhr = new goog.net.XhrIo();
		
	// adds prefix to all jsonRequest url's
	// TODO: remove tight coupling!
	url = "server/"+url;
		
	goog.events.listen(xhr, "success", function(e) {
		var xhr = e.target;
		var json = goog.json.parse( xhr.getResponseText() );
		
		// if response is exception
		if((json.exception != undefined) && (json.exception == 1)) {
			// if exception callback was defined
			if( exceptionCallback != undefined) {
				exceptionCallback( json );
			}else {
				wa1.util.ErrorHandler.ExceptionHandler( json );
			}
		}else {
			if(successCallback == undefined) {
				throw "Success callback was not defined in json request with url: " + url;
			}
			successCallback( json );
		}
	});
	goog.events.listen(xhr, "error", wa1.util.ErrorHandler.HandleXHR);
	
	xhr.send(url, method, data);
};