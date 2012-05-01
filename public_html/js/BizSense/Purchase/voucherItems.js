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
            if (taxTypeId == 0) {
                taxPercentage = 0;
                _productChanged(pElementId, taxPercentage);
            }
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
    quantity = dojo.byId("quantity[" + rowNumber + "]").value;
    beforeTax = unitPrice * quantity;
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

var quantityChanged = function() {

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
function addItemRow(initServiceItemId, initItemDescription, initQuantity, initUnitPrice, initTaxTypeId) 
{
    counter++;
    var stateSelect = new Array();
    list = stateSelect[counter];

    var oTable = dojo.byId("items_table");           // Get a handle to the table        
    var rowsLength = oTable.tBodies[0].rows.length;
    var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
    
    // Create the first cell and add the text prompt
    var newCell0 = newRow.insertCell(0);   
    var item_name = document.createElement("input");
    item_name.size = 15;      
    item_name.name = "item_name[" + counter + "]";
    item_name.id = "item_name[" + counter + "]";           
    newCell0.appendChild(item_name);

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



    //Quantity
    var newCell2 = newRow.insertCell(2); 
    var quantity = document.createElement("input");
    quantity.size = 3;

    if ((typeof(recreateItems) == 'undefined')) 
    {
        quantity.value = '1'; 
        console.log('setting the value to 1');    
    } else {
        console.log('recreate items is defined');
    }
    if ( (!isNaN(initQuantity)) && (initQuantity != null) ) 
    {
        quantity.value = initQuantity; 
        console.log('setting the value to initQuantity value ie ' + initQuantity);    
    }
    quantity.name = "quantity[" + counter + "]";
    quantity.id = "quantity[" + counter + "]";
    quantity.onchange = quantityChanged;

    newCell2.appendChild(quantity);  
    
    
    //Unit price
    var newCell3 = newRow.insertCell(3); 
    var unit_price = document.createElement("input");
    unit_price.size = 5;  
    unit_price.name = "unit_price[" + counter + "]";
    unit_price.id = "unit_price[" + counter + "]";

    console.log("populating unit pirce with initUnitPrice = " +  initUnitPrice);
    unit_price.value = initUnitPrice;
    unit_price.onchange = unitPriceChanged;
    
    newCell3.appendChild(unit_price);  


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
    var newCell4 = newRow.insertCell(4);                    
    newCell4.appendChild(tax_type_id.domNode);


    //Amount 
    var newCell5 = newRow.insertCell(5); 
    var amount = document.createElement("input");
    amount.size = 5;  
    amount.name = "amount[" + counter + "]";
    amount.id = "amount[" + counter + "]";
    amount.disabled = "disabled";
    //itemTotalBeforeTax.value = initUnitPrice * initQuantity;
    amount.value = initUnitPrice * initQuantity;
    newCell5.appendChild(amount);  


   
    //Remove item button
    var newCell6 = newRow.insertCell(6); 
    var removeItemButton = document.createElement("input");
    removeItemButton.type = "button";    
    removeItemButton.value = "Remove";    
    removeItemButton.name = "removeItemButton[" + counter + "]";
    removeItemButton.id = "removeItemButton[" + counter + "]";
    removeItemButton.onclick = removeThisRow;
    newCell6.appendChild(removeItemButton);  

}

