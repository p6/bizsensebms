/**
 *  When the validation fails, create the items user submitted in the previous request
 */
function recreateItems()
{
        dojo.forEach (returnedContactItems, function(items)  {
                addContactItemRow(items.contact_id);
            }
        );
        dojo.forEach (returnedUserItems, function(items)  {
                addUserItemRow(items.user_id);
            }
        );
        dojo.forEach (returnedLeadItems, function(items)  {
                addLeadItemRow(items.lead_id);
            }
        );

}
dojo.addOnLoad(function() { recreateItems() })
