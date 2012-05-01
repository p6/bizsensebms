function confirmDelete(sales_stage_id)
{
    var answer = confirm("Are you sure you want to delete this sales stage ID? The opportunities related to this sales stage will be deleted. The action cannot be undone.");
    if (answer) {
        window.location = "/admin/salesstage/delete/sales_stage_id/" + sales_stage_id; 
    }
}
