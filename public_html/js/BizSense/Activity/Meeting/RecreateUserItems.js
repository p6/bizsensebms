/**
 *  When the validation fails, create the items user submitted in the previous request
 */
function recreateItems()
{
        dojo.forEach (returnedItems, function(items)  {
                addUserItemRow(items.user_id);
            }
        );

        dojo.forEach (returnedItems, function(items)  {
                addUserItemRow(items.user_id);
            }
        );

        dojo.forEach (returnedItems, function(items)  {
                addUserItemRow(items.user_id);
            }
        );
}
dojo.addOnLoad(function() { recreateItems() })
