<?php
/**
 * 
 * Post Affliate Pro Class
 * @author mhightower
 *
 */
require_once 'Affiliate.php';

class PPMDPostAffiliatePro extends Affiliate {

    private $pending = false;
    private $accountID = 'default1';
    private $totalCharge = 0.00;
    private $customerNumber;
    private $testsChosen;

    public function __construct() {
        $this->type = 'PostAffiliatePro';
    }

    /**
     * Set Post Affiliate Pro account ID
     * @param string $accountID
     * @return PPMDPostAffiliatePro $this
     */
    public function setAccountID($accountID) {
        $this->accountID = $accountID;
        return $this;
    }
    /**
     * 
     * Set the transaction object
     * @param Transaction $tr
     * @return PPMDPostAffiliatePro $this
     */
    public function setTransaction(Transaction $tr) {
        try {
            $this->totalCharge = $tr->getTotal();
            $this->customerNumber = $tr->getCustomerID();
            $this->testsChosen = implode(',', $tr->getProducts());
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return $this;
    }

    /**
     * Set if sale is in pending state.
     * @return PPMDPostAffiliatePro $this
     */
    public function setPending() {
        $this->pending = true;
        return $this;
    }

    /**
     * Create javascript
     * @return PPMDPostAffiliatePro $this
     */
    protected  function setScript() {
        $script = 'document.write(unescape("%3Cscript id=%27pap_x2s6df8d%27 src=%27" + (("https:" == document.location.protocol) ? "https://" : "http://") + "partners.getstdtested.com/scripts/trackjs.js%27 type=%27text/javascript%27%3E%3C/script%3E"));' . PHP_EOL;
        $this->scripts[] = $script;
        $script = 'PostAffTracker.setAccountId("'. $this->accountID .'");' . PHP_EOL;
        $script .= 'var sale = PostAffTracker.createSale();' . PHP_EOL;
        $script .= 'sale.setTotalCost('. $this->totalCharge .');' . PHP_EOL;
        $script .= 'sale.setOrderID("' . $this->customerNumber .'");' . PHP_EOL;
        $script .= $this->getStatus();
        $script .= 'sale.setProductID("' . $this->testsChosen .'");' . PHP_EOL;
        $script .= 'PostAffTracker.register();' . PHP_EOL;
        $this->scripts[] = $script;
        return $this;
    }

    /**
     * Get the status to use in creating javascript
     * @return string
     */
    protected  function getStatus() {
        if($this->pending) {
            $payStatus = 'P';
        } else {
            $payStatus = 'A';
        }
        return 'sale.setStatus("' . $payStatus . '");' . PHP_EOL;
    }
}
