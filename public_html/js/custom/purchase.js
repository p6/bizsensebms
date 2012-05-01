dojo.require("dojo.data.ItemFileReadStore");
    dojo.require("dijit.form.FilteringSelect");
    dojo.require("dijit.form.Form");
    dojo.require("dojo.parser");
var counter = 0;
    function moreWidgets() {
        counter++;
        var stateSelect = new Array();
        list = stateSelect[counter];
        newStore = new dojo.data.ItemFileReadStore({url: '/product/jsonlist'});
        var product = new dijit.form.FilteringSelect(
        {
            id: stateSelect[counter], 
            name: "productId[" + counter + "]", 
            searchAttr: "productName",
            store: newStore  
        }, "list");

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
      newCell1.appendChild(quantity);  

    //Unit price
      var newCell2 = newRow.insertCell(2); 
      var unitPrice = document.createElement("input");
      unitPrice.size = 5;  
      unitPrice.name = "unitPrice[" + counter + "]";
      newCell2.appendChild(unitPrice);  

    //Tax type
      var newCell3 = newRow.insertCell(3); 

          var taxSelect = new Array();
     taxTypeStore = new dojo.data.ItemFileReadStore({url: '/jsonstore/taxtype'});
      var taxType = new dijit.form.FilteringSelect(
        {
            id: taxSelect[counter], 
            name: "taxTypeId[" + counter + "]", 
            searchAttr: "name",
            store: taxTypeStore  
        }, "list");

      newCell3.appendChild(taxType.domNode);  

   
    }
// dojo.addOnLoad(function() { moreWidgets()})

