<?php

/**
 *
 * Enter description here ...
 * @author mhightower
 *
 */

require_once 'MarkerDecorator.php';

class Lab {

    private $labInfo;
    private $decorator;

    public function __construct() {
        $this->setDecorator(new MarkerDecorator());
        $this->labInfo = array();
    }

    /**
     * Magic PHP function
     * Checks to see if property is present.
     * @param string $name
     * @throws ErrorException
     *
     * @assert ('id') == 'foo'
     * @assert ('notFound') == Exception
     */
    public function __get($name){
        if(array_key_exists($name, $this->labInfo)) {
            $rtn = $this->labInfo[$name];
        } else {
            throw new ErrorException('Key not found in object: ' . $name);
        }
        return $rtn;
    }

    /**
     * Set properties for class
     * @param string $name
     * @param mixed $value
     *
     * @assert ('id', 'foo') != 0
     */
    public function __set($name, $value) {
        $this->labInfo[$name] = $value;
    }

    public function setDecorator(Decorator $decorator) {
        $this->decorator = $decorator;
    }

    /**
     *
     * Enter description here ...
     * @return array $keys
     *
     * @assert () === array()
     */
    public function getLabPropertiesList() {
        $keys = array_keys($this->labInfo);
        return $keys;
    }

    /**
     * Add lab information as attrubutes to a XML object
     * @param SimpleXMLElement $xmlObject
     * @param string $name
     * @return SimpleXMLElement
     */
    public function asXMLObject(SimpleXMLElement $xmlObject, $name) {
        $marker = $xmlObject->addChild($name);
        foreach($this->getLabPropertiesList() as $key) {
            $marker->addAttribute($key, $this->_prepLabInfo($name, $key));
        }
        return $xmlObject;
    }

    private function _prepLabInfo($name, $key) {
        try {
            $labPropValue = $this->__get($key);
        } catch (ErrorException $e) {
            //@todo Should this be an empty string or complete fail or null
            $labPropValue = "";
        }
        if(empty($this->decorator)) {
            $rtn = $labPropValue;
        } else {
            $rtn = $this->decorator->show($key, $labPropValue);
        }
        return $rtn;
    }

    public function getMarker(SimpleXMLElement $xmlObject) {
        return $this->asXMLObject($xmlObject, $this->decorator->getChildTag());
    }
}
