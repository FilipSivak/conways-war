goog.provide("wa1.util.Form");

goog.require("wa1.util.JsonRequest");

goog.require("goog.dom.forms");
goog.require("goog.net.XhrIo");
goog.require("goog.Uri.QueryData");

/** 
	Handles form submission on click of button.
	Handles errors
	@constructor
*/
wa1.util.Form = function(formId, successCallback, exceptionCallback) {
	
	// retrive form element
	var form = goog.dom.getElement( formId );
	if(form == undefined) throw "Form '"+formId+"' is not defined! Wrong form id?";
	
	// retrive button
	// TODO: add input type="submit"
	var btn = goog.dom.getElementsByTagNameAndClass('button', undefined, form); 
	btn = btn[0];
	
	// bind listener on submit "button" (tag must be button)
	goog.events.listen(btn, goog.events.EventType.CLICK, function(e) {
		goog.events.Event.preventDefault( e );
		if(!form.checkValidity()) {
			alert('Form is not valid!');
			return;
		}
		
		// prepare data
		var map = goog.dom.forms.getFormDataMap(form);
		var data = goog.Uri.QueryData.createFromMap(map);
		
		var request = new wa1.util.JsonRequest();
		request.send(form.getAttribute("action"), 'POST', data,successCallback,exceptionCallback);
	});
};