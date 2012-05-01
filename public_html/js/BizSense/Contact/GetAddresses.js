/**
 * Get the billing and shipping addresses of the account
 * Using the account_id
 * And set the addresses to the billing and shipping
 * addresses in the contact create form
 */
function getAddresses(accountId) 
{
    console.log(accountId);
    dojo.xhrGet({
        url: "/account/viewdetails/format/json/account_id/" + accountId,
        load: function(response, ioArgs){
            elements = response["account"];

            dojo.byId("billing_address_line_1").value = elements.billing_address_line_1;
            dojo.byId("billing_address_line_2").value = elements.billing_address_line_2;
            dojo.byId("billing_address_line_3").value = elements.billing_address_line_3;
            dojo.byId("billing_address_line_4").value = elements.billing_address_line_4;
            dojo.byId("billing_city").value = elements.billing_city;
            dojo.byId("billing_state").value = elements.billing_state;
            dojo.byId("billing_country").value = elements.billing_country;
            dojo.byId("billing_postal_code").value = elements.billing_postal_code;

            dojo.byId("shipping_address_line_1").value = elements.shipping_address_line_1;
            dojo.byId("shipping_address_line_2").value = elements.shipping_address_line_2;
            dojo.byId("shipping_address_line_3").value = elements.shipping_address_line_3;
            dojo.byId("shipping_address_line_4").value = elements.shipping_address_line_4;
            dojo.byId("shipping_city").value = elements.shipping_city;
            dojo.byId("shipping_state").value = elements.shipping_state;
            dojo.byId("shipping_country").value = elements.shipping_country;
            dojo.byId("shipping_postal_code").value = elements.shipping_postal_code;


            return response;
        },
        error: function(response, ioArgs){
            console.log("An error occurred, with response: " + response);
            return response;
        },
        handleAs: "json"
    });
}


