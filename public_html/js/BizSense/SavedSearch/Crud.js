function handleSavedSearchDelete(savedSearchId)
{
    var answer = confirm("Are you sure?");
    if (answer) {
        window.location = "/savedsearch/delete/saved_search_id/" + savedSearchId; 
    }
 
}

function saveSearchHandle(type)
{
    search_name = dijit.byId('search_name').value;
    dojo.byId("search").action = '/savedsearch/create/name/' + search_name + '/type/' + type;
    var xhrArgs = {
        form: dojo.byId("search"),
        handleAs: "json",
        load: function(data) {
           bizsense.dom.flashMessage("Search saved");
            console.log(data);
            window.location = data['target_url'];
        },
        error: function(error) {
           bizsense.dom.flashMessage("Search could not be saved");
        }
    }
   
    //Call the asynchronous xhrPost
    var deferred = dojo.xhrPost(xhrArgs);

}
dojo.require("dijit.form.Button");
dojo.require("dijit.Dialog");
dojo.require("dijit.form.TextBox");

