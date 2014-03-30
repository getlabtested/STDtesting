<?php
require_once TEMPLATEPATH . '/functions-ppmd.php';
global $wpdb;
//Manage Javascripts
if (!is_admin()) { // instruction to only load if it is not the admin area
    $cc = new stdClass();
    $cc->file = '/js/customer-checkout.js';
    $cc->label = 'custom_script';
    $cc->req = array('jquery');
    $cc->version = false;
    $cc->inFooter = false;

    $promo = new stdClass();
    $promo->file = '/js/promo.js';
    $promo->label = 'promo';
    $promo->req = array('jquery');
    $promo->version = false;
    $promo->inFooter = true;

    $slimbox = new stdClass();
    $slimbox->file = '/js/slimbox/js/slimbox2.js';
    $slimbox->label = 'slimbox';
    $slimbox->req = array('jquery');
    $slimbox->version = false;
    $slimbox->inFooter = true;

    $co = new stdClass();
    $co->file = '/css/checkout.css';
    $co->label = 'checkout';
    $co->req = array();
    $co->version = false;
    $co->media = 'screen';

    $slimboxcc = new stdClass();
    $slimboxcc->file = '/css/slimbox/slimbox2.css';
    $slimboxcc->label = 'slimbox';
    $slimboxcc->req = array();
    $slimboxcc->version = false;
    $slimboxcc->media = 'screen';

    $stdtesting = new stdClass();
    $stdtesting->file = '/css/stdtesting.css';
    $stdtesting->label = 'stdtesting';
    $stdtesting->req = array();
    $stdtesting->version = false;
    $stdtesting->media = 'all';

    ppmdAddFile(array($cc, $promo));
    ppmdAddFile(array($co, $stdtesting), 'style');

    // register your script location, dependencies and versionfd
    /*if(file_exists($ccScriptFile)) {
    wp_register_script('custom_script',
       get_bloginfo('template_directory') . '/js/customer-checkout.js',
       array('jquery'),
       '1.0',
       true);
    // enqueue the script
    wp_enqueue_script('custom_script');
    }*/
}

function ppmdAddFile(array $scripts, $type = 'script') {
    foreach ($scripts as $script) {
        switch ($type) {
            case 'style':
                _ppmdAddStyle($script->label , $script->file, $script->req, $script->version, $script->media);
            break;
            
            default:
                _ppmdAddScript($script->label , $script->file, $script->req, $script->version, $script->inFooter);
            break;
        }
    }
}

function _ppmdAddScript($label, $scriptFile, array $req = null, $version = false, $inFooter = true) {
    $fullPathScriptFile = TEMPLATEPATH . $scriptFile;
    if(file_exists($fullPathScriptFile)) {
        $url =  get_bloginfo('template_directory') . $scriptFile;
        wp_register_script($label, $url, $req, $version, $inFooter);
        wp_enqueue_script($label);
    } else {
        ppmdCritical('JavaScript file not found: ' . $fullPathScriptFile);
    }
}

function _ppmdAddStyle($label, $styleFile, array $req = null, $version = false, $media = 'screen') {
    $fullPathStyleFile = TEMPLATEPATH . $styleFile;
    if(file_exists($fullPathStyleFile)) {
        $url =  get_bloginfo( 'stylesheet_directory' ) . $styleFile;
        wp_register_style($label, $url, $req, $version, $media);
        wp_enqueue_style($label);
    } else {
        ppmdCritical('CSS file not found: ' . $fullPathStyleFile);
    }
}

//Manage Styles
//All custom styles should be included here
function customerCheckoutStyles() {
    if(!is_admin()) {
        //Checkout page
        $checkoutStyleURL = get_bloginfo( 'stylesheet_directory' ) . '/css/checkout.css';
        $checkoutStyleFile = TEMPLATEPATH . '/css/checkout.css';
        
        if(file_exists($checkoutStyleFile)) {
            wp_register_style(
                'checkout',
                $checkoutStyleURL,
                array(),
                '0.1',
                'screen'
            );
            wp_enqueue_style('checkout');
        } else {
            error_log('Could not find stylesheet: ' . $checkoutStyleFile);
        }
    }
}
//add_action('wp_print_styles', 'customerCheckoutStyles');

// Promo Code functions
function applyPromoCode($promoCode) {
    if (isPromoCodeValid($promoCode)) {
        $_SESSION['promoCode'] = $promoCode;
        return true;
    } else {
        unset($_SESSION['promoCode']);
        ppLog('Invalid promo code was tried: ' . $promoCode);
        return false;
    }
}

function showPromoMsg($discount = '', $originalPrice = 0, $discountedPrice = 0) {
    $rtn = '';
    if($discount) {
        $rtn = '<tr><td colspan="2" style="font-weight:bold; text-align:right; font-size:15px; padding-top:10px;" id="priceTD"><span style="display:inline">' . $discount .' discount has been applied</span><br />Was <span style="text-decoration: line-through;display:inline;color:#999">$ ' . number_format($originalPrice,2) .'</span></td></tr>';
    }
    return $rtn;
}

function invalidPromoCodeMsg() {
    return '<span id="invalidPromo" style="color:red;display:inline">Invalid Promo Code</span>';
}

/**
 * isPromoCodeValid
 * Is the promo code a valid one.
 * @param string $promoCode
 * @return bool
 */
function isPromoCodeValid($promoCode) {
    $today = strtotime('now');
    $rslt = getPromoCode($promoCode);
    $start = strtotime($rslt['start_date']);
    $end = strtotime($rslt['end_date']);
    if(null === $rslt || $today < $start || $today > $end) {
        return false;
    }
    return true;
}

/**
 * Retrieve promo code info.
 * Get promo code information from promo_codes table.
 * @param string $promoCode
 * @return array Hash of promo code row
 */
function getPromoCode($promoCode) {
    global $wpdb;
    if(empty($promoCode)) {
        ppmdWarning('Promo code given was empty.');
        return false;
    }
    $query = 'SELECT * FROM promo_codes WHERE code LIKE %s';
    $result = $wpdb->get_row($wpdb->prepare($query, $promoCode), ARRAY_A);
    return $result;
}

/**
 * isPrevPaidPromoCodeCustomer
 * Check if conformation code and promo code is valid
 * @param string $conformationId customer conformation id
 * @param string $promoCode promo code
 * @param string $startDate optional date YYYY-MM-DD
 * @param string $endDate optional date YYYY-MM-DD
 * @return bool
 */
function isPrevPaidPromoCodeCustomer($conformationId, $promoCode, $startDate = '2011-02-10', $endDate = '2011-02-15') {
    $query = "SELECT * FROM user_test_results WHERE date(created)>=%s and date(created)<=%s AND system='production' AND testOrder=0 AND cc_validation='success' AND confirmationnumber=%s AND refund=0";
    $results = $wpdb->get_results($wpdb->prepare($query, $startDate, $endDate, $promoCode));
    if(null === $results){
        return false;
    }
    if(isUsedPromoCode($promoCode, $startDate)){
        return false;
    }
    return true;
}

/**
 * isUsedPromoCode
 * Has the promocode/confirmationnumber been used
 * @param string $promoCode
 * @param string $startDate
 * @return bool
 */
function isUsedPromoCode($promoCode, $startDate = '2011-02-10') {
    $query = "SELECT * FROM user_test_results WHERE date(created)>=%s AND system='production' AND testOrder=0 AND pwn_creation='success' AND promoID=%s"; //@todo dates are hardcoded needs updating.
    $results = $wpdb->get_results($wpdb->prepare($query, $startDate, $promoCode));
    if(null === $results){
        return false;
    }
    return true;
}

function domainNameRedir() {
/*    $hostName = $_SERVER['HTTP_HOST'];
    $hostNameA = explode(".", $hostName);
    $domainName = $hostNameA[1];
    $domainName = strtolower($domainName);
    $domainNameRedir = $domainName .'.com';
    return $domainNameRedir;
    */
    $url = parse_url($_SERVER['HTTP_HOST']);
    if(false === $url) {
        ppLog('Url did not parse: ' . $_SERVER['HTTP_HOST']);
        return false;
    }
    return $url['host'];
}

/**
 * Get Affliate or default phone number for customer checkout page
 */
function getAffliatePhone() {
    if(empty($_SESSION['affPhone'])) {
        $rtn = '866-749-6269';
    } else {
        $rtn = $_SESSION['affPhone'];
    }
    return $rtn;
}

function getPromoInput($promoCode, $promoMsg) {
    require_once TEMPLATEPATH . '/customer-checkout/pvPromoInput.php';
}

function displayPromo($totalcost, $totalcostDisplay) {
    if($totalcost == $totalcostDisplay) {
        $rtn = 'Total';
    } else {
        $rtn = 'New Total: &nbsp;&nbsp;<span style="text-decoration: line-through;display:inline;color:red">$' . number_format($totalcost,2) . '</span>';
    }
    return $rtn;
}
