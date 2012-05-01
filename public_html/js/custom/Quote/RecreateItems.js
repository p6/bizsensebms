/*
 *  * When the validation fails, create the items user submitted in the previous request
 *   */
function recreateItems()
{
        dojo.forEach (returnedItems, function(items)  {
                moreWidgets(items.productName, items.quantity, items.unitPrice, items.taxTypeName, items.taxPercentage);
            }
        );

}
dojo.addOnLoad(function() { recreateItems() })

