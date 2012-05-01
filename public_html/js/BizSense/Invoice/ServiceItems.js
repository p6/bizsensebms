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
    
   newProductStore = new dojo.data.ItemFileReadStore({url: '/serviceproduct/jsonstore'});
   taxStore = new dojo.data.ItemFileReadStore({url: '/finance/tax/store'});
    //Fetch selling price and set value to the unit_price field
    newProductStore.fetchItemByIdentity({
        identity: pValue, 
        onItem: function(item)
        {
            sellingPrice = newProductStore.getValue(item,"amount");
            sellingPriceTempStorage = sellingPrice;
            unit_price = dojo.byId("unit_price[" + pElementId + "]");
            unit_price.value = sellingPrice;
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

/**
 * We have to wait till the tax percentage is obtained from asynchronous call
 * once we have the tax percentage update the amount field
 */
function _productChanged(rowNumber, taxPercentage)
{
    unitPrice = dojo.byId("unit_price[" + rowNumber + "]").value;
    beforeTax = unitPrice * 1;
    tax = (beforeTax * taxPercentage) / 100;
    lineTotal = beforeTax + tax;
    amount = dojo.byId('amount[' + rowNumber  + ']');
    amount.value = lineTotal;

}


var taxChanged = function() {
    var pName = this.name;
    pElementId = pName.split("[")[1].split("]")[0];   
    updateRowTotal(pElementId);

}


/**
 * Fills the selling price column
 */
var unitPriceChanged = function(){
    quantityElementId = this.id.split("[")[1].split("]")[0];   
    updateRowTotal(quantityElementId);
}


function updateRowTotal(rowNumber)
{
    console.log("in updateRowTotal row number is " + rowNumber);

    taxStore = new dojo.data.ItemFileReadStore({url: '/finance/tax/store'});
    taxStore.fetchItemByIdentity(
            {
                identity: dijit.byId("tax_type_id_" + rowNumber).attr('value'),
                onItem: function(item) 
                {
                    taxPercentage = taxStore.getValue(item, "percentage");    
                    _productChanged(rowNumber, taxPercentage);
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
function addItemRow(initServiceItemId, initItemDescription, initUnitPrice, initTaxTypeId) 
{
    counter++;
    var stateSelect = new Array();
    list = stateSelect[counter];

    newStore = new dojo.data.ItemFileReadStore({url: '/serviceproduct/jsonstore'});
    var product = new dijit.form.FilteringSelect(
    {
            id: "product_id_" + counter, 
            name: "product_id[" + counter + "]", 
            searchAttr: "name",
            store: newStore,
           // store: fStore,
            onChange: productChanged 
    }, list);
    
    if (initServiceItemId != null) {
        if ( isNaN(initServiceItemId) == false)
        product.attr('value', initServiceItemId);
    }

    var oTable = dojo.byId("items_table");           // Get a handle to the table        
    var rowsLength = oTable.tBodies[0].rows.length;
    var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
    
    // Create the first cell and add the text prompt
    var newCell0 = newRow.insertCell(0);                    
    newCell0.appendChild(product.domNode);


    //Description
    var newCell1 = newRow.insertCell(1); 
    var item_description = document.createElement("input");
    item_description.size = 15;

    if (initItemDescription != null ) 
    {
        item_description.value = initItemDescription; 
    }
    item_description.name = "item_description[" + counter + "]";
    item_description.id = "item_description[" + counter + "]";

    newCell1.appendChild(item_description);  
        
    //Unit price
    var newCell2 = newRow.insertCell(2); 
    var unit_price = document.createElement("input");
    unit_price.size = 5;  
    unit_price.name = "unit_price[" + counter + "]";
    unit_price.id = "unit_price[" + counter + "]";

    console.log("populating unit pirce with initUnitPrice = " +  initUnitPrice);
    unit_price.value = initUnitPrice;
    unit_price.onchange = unitPriceChanged;
    
    newCell2.appendChild(unit_price);  


    taxStore = new dojo.data.ItemFileReadStore({url: '/finance/tax/store'});
    var tax_type_id = new dijit.form.FilteringSelect(
    {
            id: "tax_type_id_" + counter, 
            name: "tax_type_id[" + counter + "]", 
            searchAttr: "name",
            store: taxStore,
            onChange: taxChanged 
    });
    
    if (initTaxTypeId != null) {
        if ( isNaN(initTaxTypeId) == false)
        tax_type_id.attr('value', initTaxTypeId);
    }

    // Create the first cell and add the text prompt
    var newCell3 = newRow.insertCell(3);                    
    newCell3.appendChild(tax_type_id.domNode);


    //Amount 
    var newCell4 = newRow.insertCell(4); 
    var amount = document.createElement("input");
    amount.size = 5;  
    amount.name = "amount[" + counter + "]";
    amount.id = "amount[" + counter + "]";
    amount.disabled = "disabled";
    amount.value = initUnitPrice * 1;
    newCell4.appendChild(amount); 


   
    //Remove item button
    var newCell5 = newRow.insertCell(5); 
    var removeItemButton = document.createElement("input");
    removeItemButton.type = "button";    
    removeItemButton.value = "Remove";    
    removeItemButton.name = "removeItemButton[" + counter + "]";
    removeItemButton.id = "removeItemButton[" + counter + "]";
    removeItemButton.onclick = removeThisRow;
    newCell5.appendChild(removeItemButton);  

}

