<?php
/**
 *
 * Abstract class for decorators of labs XML output
 * @author mhightower
 *
 */
abstract class Decorator {
    /**
     *
     * Returns the name of the decorator
     */
    abstract public function getChildTag();
    /**
     * Manipulate the string given according to some
     * rules defined based on the name.
     * @param string $name A key
     * @param string $data A value
     * @return string Converted string
     */
    abstract public function show($name, $data);
}
