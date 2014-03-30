<?php
/**
 * Customer transaction data struct
 * @author mhightower <mhightower@pinpointmd.com>
 *
 */
class Transaction {
    /**
     * Total charge
     * @var float
     */
    protected $total;
    /**
     * Customer ID
     * @var string
     */
    protected $customerID;
    /**
     * Product list
     * @var array
     */
    protected $productList;
    /**
     * Get total charge
     * @return float
     */
    public function getTotal() {
        return $this->total;
    }
    /**
     * Get Pinpoint MD customer ID
     * @return string|int
     */
    public function getCustomerID() {
        return $this->customerID;
    }
    /**
     * List of products
     * @return array
     */
    public function getProducts() {
        return $this->productList;
    }
    /**
     * Get customer ordered tests in string form
     * @return string
     */
    public function getProductsString() {
        $rtn = implode(' ', $this->productList);
        return $rtn;
    }
    /**
     * Set Customer ID
     * @param string $custID
     * @return Transaction
     */
    public function setCustomerID($custID) {
        $this->customerID = $custID;
        return $this;
    }
    /**
     * Set ordered tests with array
     * 
     * @param array $products list of products
     * @return Transaction
     * 
     * @todo Clean prouducts in array if blank then down place it in array.
     *
     */
    public function setProducts(Array $products) {
        $this->productList = $products;
        return $this;
    }
    /**
     * Set total charge
     * @param float $total
     * @return Transaction
     *
     */
    public function setTotal($total) {
        $this->total = $total;
        return $this;
    }
}