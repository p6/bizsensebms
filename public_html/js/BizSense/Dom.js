/**
 * BizSense crud library
 */
bizsense.dom = new Object();

bizsense.dom.flashMessage = function(message) {
   if (dojo.byId("flash_message")) {
    
   } else {
        dojo.create("div", {"id":"flash_message"}, "flash_message_container");
   }

    var messageNode = dojo.doc.createTextNode(message);
    dojo.place(messageNode, "flash_message", "last");
    dojo.create("br", null, "flash_message","last");
}
