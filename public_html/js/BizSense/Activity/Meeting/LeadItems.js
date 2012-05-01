dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.Form");
dojo.require("dojo.parser");

/*
 * Removes one row or items
 */
var removeThisRow = function() {

    //Removes the TR node using the removeThisRowButtonId     
    buttonElementId = this.id;
    parentTd = this.parentNode;
    parentTr = parentTd.parentNode;
    dojo._destroyElement(parentTr);
}

var productChanged = function() {
    var pName = this.name;

  //pValue is the productId selected by the user  
  var pValue = this.value;  
  //pId has the productId   
  
  /**
   * pElementId is the row id on which we are operating
   */
   pElementId = pName.split("[")[1].split("]")[0];   
   var sellingPriceTempStorage;
    
   newProductStore = new dojo.data.ItemFileReadStore({url: '/product/jsonstore'});
   taxStore = new dojo.data.ItemFileReadStore({url: '/finance/tax/store'});
    //Fetch selling price and set value to the unit_price field
    newProductStore.fetchItemByIdentity({
        identity: pValue, 
        onItem: function(item)
        {
            sellingPrice = newProductStore.getValue(item,"unit_price");
            sellingPriceTempStorage = sellingPrice;
            unit_price = dojo.byId("unit_price[" + pElementId + "]");
            unit_price.value = sellingPrice;
            quantity = dojo.byId('quantity[' + pElementId  + ']');
            taxTypeId = newProductStore.getValue(item, "tax_type_id");
            dijit.byId("tax_type_id_" + pElementId).attr('value', taxTypeId);

            taxStore.fetchItemByIdentity(
            {
                identity: taxTypeId,
                onItem: function(item) 
                {
                    taxPercentage = taxStore.getValue(item, "percentage");    
                    _productChanged(pElementId, taxPercentage);
                }
            });
        }
    });
}


/*
 * Keeps track of number of rows in the table
 */
var counter = 0;
var consultantCounter = 0;

/*
 * Creates one row of item form elements
 */    
function addLeadItemRow(initServiceItemId, initItemDescription, initQuantity, initUnitPrice, initTaxTypeId) 
{
    counter++;
    var stateSelect = new Array();
    list = stateSelect[counter];

    newStore = new dojo.data.ItemFileReadStore({url: '/lead/jsonstore'});
    var lead = new dijit.form.FilteringSelect(
    {
            id: "lead_id_" + counter, 
            name: "lead_id[" + counter + "]", 
            searchAttr: "lead",
            store: newStore,
           // store: fStore,
            onChange: productChanged 
    }, list);
    
    if (initServiceItemId != null) {
        if ( isNaN(initServiceItemId) == false)
        lead.attr('value', initServiceItemId);
    }

    var oTable = dojo.byId("lead_items_table");           // Get a handle to the table        
    var rowsLength = oTable.tBodies[0].rows.length;
    var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
    
    // Create the first cell and add the text prompt
    var newCell0 = newRow.insertCell(0);                    
    newCell0.appendChild(lead.domNode);

    //Remove item button
    var newCell1 = newRow.insertCell(1); 
    var removeItemButton = document.createElement("input");
    removeItemButton.type = "button";    
    removeItemButton.value = "Remove";    
    removeItemButton.name = "removeItemButton[" + counter + "]";
    removeItemButton.id = "removeItemButton[" + counter + "]";
    removeItemButton.onclick = removeThisRow;
    newCell1.appendChild(removeItemButton);  
}

