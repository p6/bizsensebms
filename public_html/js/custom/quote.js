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
 * Fills the selling price column
 */
var fillBeforeTaxPrice = function(){
    quantityElementId = this.id.split("[")[1].split("]")[0];   
    priceBeforeTax = this.value * dojo.byId("unitPrice[" + quantityElementId + "]").value ;
    dojo.byId("itemTotalBeforeTax[" + quantityElementId +  "]").value = priceBeforeTax;
    fillAfterTaxPrice(quantityElementId);
}

/*
 * Fills price after tax type, quantity and unit price are available
 */
function fillAfterTaxPrice(elementNumber) {
    var productStore = new dojo.data.ItemFileReadStore({url: '/product/jsonstore'});
    var productId = dijit.byId("productId_"+elementNumber).value;
    productStore.fetchItemByIdentity({
    identity: productId, 
    onItem: function(item)
    {
        taxPercentage = productStore.getValue(item,"percentage");
        console.log('Logging after tax price');
        itemTotalBeforeTax = dojo.byId("itemTotalBeforeTax[" + elementNumber + "]").value;
        taxAmount = (itemTotalBeforeTax * (taxPercentage/100)); 
        /*
         * Cast the values to numeric and then multiply
         */        
        itemTotalAfterTax = dojo.byId("itemTotalAfterTax[" + elementNumber  + "]").value = +itemTotalBeforeTax + +taxAmount;
    }
    });
}



/*
 * Creates <th> elements in the productsTable
 */
function setProductsTableHeaders(){
   var oTable = dojo.byId("productsTable");           // Get a handle to the table        
   var rowsLength = oTable.tBodies[0].rows.length;
   var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
  
   //Product Header
   var newCell0 = newRow.insertCell(0); 
   var productHeader = document.createTextNode("Product");
   newCell0.appendChild(productHeader);  
 
   //Quantity header
   var newCell1 = newRow.insertCell(1); 
   var quantityHeader = document.createTextNode("Quantity");
   newCell1.appendChild(quantityHeader);  

   //Unit Price header
   var newCell2 = newRow.insertCell(2); 
   var unitPriceHeader = document.createTextNode("Unit Price");
   newCell2.appendChild(unitPriceHeader);  

   //Total Before Tax header
   var newCell3 = newRow.insertCell(3); 
   var itemPriceBeforeTaxHeader = document.createTextNode("Price Before Tax");
   newCell3.appendChild(itemPriceBeforeTaxHeader);  

   //tax type header
   var newCell4 = newRow.insertCell(4); 
   var taxTypeHeader = document.createTextNode("Tax Type");
   newCell4.appendChild(taxTypeHeader);  


   //Item price After Tax header
   var newCell5 = newRow.insertCell(5); 
   var itemPriceAfterTaxHeader = document.createTextNode("Price After Tax");
   newCell5.appendChild(itemPriceAfterTaxHeader);  

   //Action column header
   var newCell6 = newRow.insertCell(6); 
   var actionHeader = document.createTextNode("Action");
   newCell6.appendChild(actionHeader);  

}

/*
 * Keeps track of number of rows in the table
 */
var counter = 0;

var fillSellingPrice = function() {
  var pName = this.name;

  //pValue is the productId selected by the user  
  var pValue = this.value;  
  //pId has the productId   
   pElementId = pName.split("[")[1].split("]")[0];   
        
   newProductStore = new dojo.data.ItemFileReadStore({url: '/product/jsonstore'});
    //Fetch selling price and set value to the unitPrice field
    newProductStore.fetchItemByIdentity({
    identity: pValue, 
    onItem: function(item)
    {
        sellingPrice = newProductStore.getValue(item,"selling_price");
        unitPrice = dojo.byId("unitPrice[" + pElementId + "]");
        unitPrice.value = sellingPrice;
    }
    });

    //Fetch tax type and set value to the taxType field
    newProductStore.fetchItemByIdentity({
    identity: pValue, 
    onItem: function(item)
    {
        taxType = newProductStore.getValue(item,"name");
        taxDijit = dijit.byId("taxSelect_" + pElementId);
        taxDijit.setDisplayedValue(taxType);
    }
    });

}

/*
 * Creates one row of item form elements
 */    
function moreWidgets(initProductName, initQuantity, initUnitPrice, initTaxTypeName, initTaxPercentage) 
{
    counter++;
    var stateSelect = new Array();
    list = stateSelect[counter];

    newStore = new dojo.data.ItemFileReadStore({url: '/product/jsonstore'});
    var product = new dijit.form.FilteringSelect(
    {
            id: "productId_" + counter, 
            name: "productId[" + counter + "]", 
            searchAttr: "product_name",
            store: newStore,
           // store: fStore,
            onChange: fillSellingPrice 
    }, list);
    
    if (initProductName != null) {
        product.setDisplayedValue(initProductName);
    }

    var oTable = dojo.byId("productsTable");           // Get a handle to the table        
    var rowsLength = oTable.tBodies[0].rows.length;
    var newRow = oTable.tBodies[0].insertRow(rowsLength-1); // insert a row for the Other text input.
    
    // Create the first cell and add the text prompt
    var newCell0 = newRow.insertCell(0);                    
    newCell0.appendChild(product.domNode);

    //Quantity
    var newCell1 = newRow.insertCell(1); 
    var quantity = document.createElement("input");
    quantity.size = 3; 
    quantity.name = "quantity[" + counter + "]";
    quantity.id = "quantity[" + counter + "]";
    quantity.onchange = fillBeforeTaxPrice;

    quantity.value = initQuantity;    

    newCell1.appendChild(quantity);  
    
    
    //Unit price
    var newCell2 = newRow.insertCell(2); 
    var unitPrice = document.createElement("input");
    unitPrice.size = 5;  
    unitPrice.name = "unitPrice[" + counter + "]";
    unitPrice.id = "unitPrice[" + counter + "]";

    unitPrice.value = initUnitPrice;
    
    newCell2.appendChild(unitPrice);  

    //Item Total Before Tax
    var newCell3 = newRow.insertCell(3); 
    var itemTotalBeforeTax = document.createElement("input");
    itemTotalBeforeTax.size = 5;  
    itemTotalBeforeTax.name = "itemTotalBeforeTax[" + counter + "]";
    itemTotalBeforeTax.id = "itemTotalBeforeTax[" + counter + "]";
    itemTotalBeforeTax.disabled = "disabled";
    //itemTotalBeforeTax.value = initUnitPrice * initQuantity;
    itemTotalBeforeTax.value = initUnitPrice * initQuantity;
    newCell3.appendChild(itemTotalBeforeTax);  


    //Tax type
    var newCell4 = newRow.insertCell(4); 

    var taxSelect = new Array();
    taxTypeStore = new dojo.data.ItemFileReadStore({url: '/jsonstore/taxtype'});
    var taxType = new dijit.form.FilteringSelect(
    {
        id: "taxSelect_" + counter, 
        name: "taxTypeId[" + counter + "]", 
        searchAttr: "name",
        store: taxTypeStore  
    }, list);
    
    if (initTaxTypeName != null) {
        taxType.setDisplayedValue(initTaxTypeName);    
    }

    newCell4.appendChild(taxType.domNode);  

    //Item Total After Tax
    var newCell5 = newRow.insertCell(5); 
    var itemTotalAfterTax = document.createElement("input");
    itemTotalAfterTax.size = 5;  
    itemTotalAfterTax.name = "itemTotalAfterTax[" + counter + "]";
    itemTotalAfterTax.id = "itemTotalAfterTax[" + counter + "]";
    itemTotalAfterTax.disabled = "disabled";

    if (initProductName != null) {
        taxAmount = (itemTotalBeforeTax.value * (initTaxPercentage/100)); 
    console.log('here is ' + taxAmount);
        itemTotalAfterTax.value = +taxAmount + +itemTotalBeforeTax.value;
    }

    newCell5.appendChild(itemTotalAfterTax);  

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

/*
 * As soon as the page loads, create the header in the products table
 */
//dojo.addOnLoad(function() { setProductsTableHeaders() })
dojo.addOnLoad(setProductsTableHeaders);
