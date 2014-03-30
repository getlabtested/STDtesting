/**
 * Promo 
 */
jQuery(document).ready(function () {
    var showPromo = document.posForm.showPromo.value;

    jQuery('#email').blur(function () {
        var emailVal, fnameVal, lnameVal;
        emailVal = jQuery('#email').val();
        fnameVal = jQuery('#fname').val();
        lnameVal = jQuery('#lname').val();
        jQuery("#emailCapture").load("email-capture.php?email=" +
            emailVal +
            "&fname=" +
            fnameVal +
            "&lname=" +
            lnameVal);
    });

    //Don't submit until modal is closed
    jQuery('#lbCloseLink').click(function (e){
        jQuery('#promoForm').submit();
    });
    // Fade out Invalid promo message
    if(jQuery("#invalidPromo")) {
        jQuery("#invalidPromo").fadeOut(3000);
    }
});
/*jslint white: true, onevar: true, undef: true, nomen: true, regexp: true, plusplus: true, bitwise: true, newcap: true, browser: true, maxerr: 50, indent: 4 */
