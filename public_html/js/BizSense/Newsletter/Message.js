function sendTestMessage()
{
    var xhrArgs = {
        form: dojo.byId("test_message"),
        handleAs: "json",
        load: function(data) {
           // dojo.byId("flash_message").innerHTML = "Test messages have been successfully sent";
           bizsense.dom.flashMessage("Test message has been successfully sent");
        },
        error: function(error) {
           // dojo.byId("flash_message").innerHTML = "Test messages could not be sent";
           bizsense.dom.flashMessage("Test message could not be sent");
        }
    }
   
    //Call the asynchronous xhrPost
    var deferred = dojo.xhrPost(xhrArgs);
    
    //scroll to top of page
    window.scrollTo(0,0);
}


