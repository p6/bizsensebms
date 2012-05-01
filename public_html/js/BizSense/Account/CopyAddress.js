/**
 * Copy the address from billing address to shipping address
 */
function copyBillingAddress()
{
    if (!
            (dojo.byId("copy_from_billing").checked == true)
        ) {
        return;
    }
    dojo.byId("shipping_address_line_1").value = dojo.byId(
        "billing_address_line_1").value;
    dojo.byId("shipping_address_line_2").value = dojo.byId(
        "billing_address_line_2").value;
    dojo.byId("shipping_address_line_3").value = dojo.byId(
        "billing_address_line_3").value;
    dojo.byId("shipping_address_line_4").value = dojo.byId(
        "billing_address_line_4").value;
    dojo.byId("shipping_city").value = dojo.byId(
        "billing_city").value;
    dojo.byId("shipping_state").value = dojo.byId(
        "billing_state").value;
    dojo.byId("shipping_postal_code").value = dojo.byId(
        "billing_postal_code").value;
    dojo.byId("shipping_country").value = dojo.byId(
        "billing_country").value;

}

