<?php
require_once ABSPATH . 'wp-load.php';

//Manage JavaScript library
function ppInit() {
    $pages[] = 'select-testing-center';
    $pages[] = 'locate-testing-center';
    if(is_page($pages)) {
        wp_enqueue_script('jquery');
    }
}
add_action('init', 'ppInit');

/**
 * 
 * PinPoint custom logger.
 * @param string $message Error message
 * @param int $severity optional severity
 */
function ppLog($message, $severity = 0) {
    error_log($message, $severity);
}

/**
 * ppSessionPopulate
 * Add data to session.
 * @param array $post Normaly the $_POST data but can be any hash array of data.
 * @param array $whiteList key array of that should be added to session.
 */
function ppSessionPopulate($post, $whiteList) {
    foreach ($post as $postKey => $postValue) {
        if(in_array($postKey, $whiteList)) {
            $_SESSION[$postKey] = $postValue;
        }
    }
    return true;
}

/**
 * ppClean
 * Clean user input
 * @param string $str User input from $_POST and $_SESSION
 * @return string
 */
function ppClean($str) {
    $str = mb_convert_encoding($str, "UTF-8", "UTF-8");
    $str = htmlentities($str, ENT_QUOTES, "UTF-8");
    return $str;
}

function ppSetupPartnerPromo() {
    global $wpdb; //Needed here for some reason
    /** NEW Partner Phone,Image,Doc call to local DB **/
    if(isset($_GET['a_aid'])) {
        $assoc_id = $_GET['a_aid'];
        $_SESSION['refid'] = $_GET['a_aid'];
    }elseif (isset($_SESSION['refid'])) {
        $assoc_id = $_SESSION['refid'];
    } else {
        return false;
    }
    if($assoc_id) {
        $sql = "SELECT affiliatePhone, partnerImg, docImage, promoID FROM sources WHERE sourceID=%s";
        $row = $wpdb->get_row( $wpdb->prepare($sql, $assoc_id));
        if (null !== $row) {
            $value = $row->affiliatePhone;
            $_SESSION['affPhone'] = $value;
            $value = $row->partnerImg;
            $_SESSION['affImgLink'] = $value;
            $value = $row->docImage;
            $_SESSION['docImage'] = $value;

            /**
             * @TODO check and see why vars aren't passing down into header.php
             * looks like session var can't be set from the db $row either
             */
            $_SESSION['promoID'] = $row->promoID;
        }

        if (isset($_SESSION['promoID'])) {
            //$query = "SELECT * FROM promo_codes WHERE id='$promoID'";
            $sql = "SELECT * FROM promo_codes WHERE id=%s";
            //if (!$result = mysql_query($query)) {
            //    die ("Query failed: $query");
            //}
            //$row = mysql_fetch_array($result);
            $row = $wpdb->get_row($wpdb->prepare($sql, $_SESSION['promoID']), ARRAY_A);
            if(null !== $row) {
                $discountAmount = $row['discount_amount'];
                $discountPerc = $row['discount_percentage'];
                 
                if ($discountAmount){
                    $_SESSION['discountAmount'] = $discountAmount;
                }
                if ($discountPerc){
                    $_SESSION['discountPerc'] = $discountPerc;
                }
            }
        }
    }
}

/**
 *
 * Used when error is not fatal.
 * @param string $message
 */
function ppmdWarning($message) {
    ppLog($message);
}

/**
 * 
 * Critical logging
 * @param string $message
 * @param bool $adminEmail
 */
function ppmdCritical($message, $adminEmail = true) {
    ppLog($message);
    if($adminEmail) {
        ppmdSendAdminEmail('Critical', "$message");
    }
}

function ppmdNotice($message) {
    ppLog($message);
}

/**
 *
 * Send Admin and Operations email
 * @param string $emailSubject
 * @param string $emailMessage
 * @param string $emailHeaders
 * @param string $parameters
 * @todo add more email addresses.
 */
function ppmdSendAdminEmail($emailSubject, $emailMessage, $emailHeaders = null, $parameters = null) {
    $emailToAddress = adminEmail();
    if(mail($emailToAddress, $emailSubject, $emailMessage, $emailHeaders, $parameters)) {
        ppmdNotice('eMail sent: ' . $emailSubject);
        return true;
    } else {
        ppmdCritical('eMail not sent: ' . $emailToAddress . ' ' . $emailSubject, false);
        return false;
    }
}
function ppmdTestServerSwitch($testSession) {
    if($_SESSION[$testSession]) {
        $testSessionIdx = strtoupper($testSession);
        $testSrvrIdxs = array(
        'END_POINT',
        'USR_QD',
        'USR_HOME',
        'USR_LC',
        'USR_PSC',
        'USR_PASS');
        foreach ($testSrvrIdxs as $testSrvrIdx) {
            if(isset($_SERVER[$testSrvrIdx
            . '_'
            . $testSessionIdx])) {
            $_SERVER[$testSrvrIdx] = $_SERVER[$testSrvrIdx
            . '_'
            . $testSessionIdx];
            }
        }
    }
}
function ppmdTestBorder($testSession) {
    $rtn = "";
    if(isset($_SESSION[$testSession])) {
        $rtn .= '<style type="text/css" media="screen">' . PHP_EOL;
        $rtn .= '#testTop, #testBottom, #testLeft, #testRight { background: red; position: fixed;}' . PHP_EOL;
        $rtn .= '#testLeft, #testRight {top: 0; bottom: 0;width: 15px;}' . PHP_EOL;
        $rtn .= '#testLeft { left: 0;}' . PHP_EOL;
        $rtn .= '#testRight { right: 0; }' . PHP_EOL;
        $rtn .= '#testTop, #testBottom {left: 0; right: 0; height: 15px;}' . PHP_EOL;
        $rtn .= '#testTop { top: 0; }' . PHP_EOL;
        $rtn .= '#testBottom { bottom: 0;}' . PHP_EOL;
        $rtn .= '</style>' . PHP_EOL;
        $rtn .= '<div id="testLeft"></div>' . PHP_EOL;
        $rtn .= '<div id="testRight"></div>' . PHP_EOL;
        $rtn .= '<div id="testTop"></div>' . PHP_EOL;
        $rtn .= '<div id="testBottom"></div>' . PHP_EOL;
    }
    return $rtn;
}
/**
 *
 * Admin email address
 */
function adminEmail() {
    if(empty($_SERVER['ADMIN_EMAIL'])) {
        return 'systems@dtcmd.com';
    } else {
        return $_SERVER['ADMIN_EMAIL'];
    }
}

/**
 * Returns current domain name (google.com)
 */
function ppmdDomainName() {
    $domain = parse_url(site_url());
    return $domain['host'];
}

/**
 * 
 * Enter description here ...
 */
function ssl() {
    if(empty($_SERVER['HTTPS'])) {
        echo rtrim($_SERVER['CDN_PATH'],"/");
    } else {
        echo bloginfo('template_directory') . '/images';
    }
    return;
}
