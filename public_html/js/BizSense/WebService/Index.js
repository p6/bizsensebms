function confirmDelete(ws_application_id)
{
    var answer = confirm("Are you sure you want to delete this application?");
    if (answer) {
        window.location = "/admin/webservice/delete/ws_application_id/" + ws_application_id; 
    }
}
