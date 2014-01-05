goog.provide("wa1.util.Binder");

goog.require("goog.array");

wa1.util.Binder.bind = function (tagName,className,bindScope, callback) {
	var bindScope = goog.dom.getElement(bindScope);
	
	var nodeList = goog.dom.getElementsByTagNameAndClass(tagName, className, bindScope);
	goog.array.forEach(nodeList, function(el){
		goog.events.listen(el, goog.events.EventType.CLICK, callback);
	});	
};