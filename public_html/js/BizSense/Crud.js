/**
 * BizSense crud library
 */
bizsense.crud = new Object();

bizsense.crud.deleteBasic = function(urlToRedirect) {
    var answer = confirm("Are you sure? The action cannot be undone.");
    if (answer) {
        window.location = urlToRedirect; 
    }
}
