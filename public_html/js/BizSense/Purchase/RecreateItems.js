/**
 *  When the validation fails, create the items user submitted in the previous request
 */
function recreateItems()
{
        dojo.forEach (returnedItems, function(items)  {
                addItemRow(items.product_id, items.item_description, items.quantity, items.unit_price, items.tax_type_id);
            }
        );

}
dojo.addOnLoad(function() { recreateItems() })
