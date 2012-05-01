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


/*
 * Keeps track of number of rows in the table
 */
var counter = 0;

/*
 * Creates one row of item form elements
 */    
function addUserItemRow(initServiceItemId, initItemDescription, initQuantity, initUnitPrice, initTaxTypeId) 
{
    counter++;
    var stateSelect = new Array();
    list = stateSelect[counter];

    newStore = new dojo.data.ItemFileReadStore({url: '/user/jsonstore'});
    var user = new dijit.form.FilteringSelect(
    {
            id: "user_id_" + counter, 
            name: "user_id[" + counter + "]", 
            searchAttr: "email",
            store: newStore,
           // store: fStore,
            onChange: productChanged 
    }, list);
    
    if (initServiceItemId != null) {
        if ( isNaN(initServiceItemId) == false)
        user.attr('value', initServiceItemId);
    }

    var oTable = dojo.byId("user_items_table");           // Get a handle to the table        
    var rowsLength = oTable.tBodies[0].rows.length;
    var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
    
    // Create the first cell and add the text prompt
    var newCell0 = newRow.insertCell(0);                    
    newCell0.appendChild(user.domNode);

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

