<?php

/**
 * Model Abstract
 *
 * @package SellBooksDirect
 * @subpackage Library
 * @version $Id: 548ac62ecdafde69ab04f25dec9bf76a01db94e6 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Common model functionality
 *
 * @package SellBooksDirect
 * @subpackage Library
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
abstract class My_ModelAbstract
{

    /**
     * Set inital properties
     *
     * @param $options array
     *            Initial properties you want to set
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->exchangeArray($options);
        }
    }

    /**
     * Magic set function, to enforce use of setters
     *
     * @param $name string
     *            Property name
     * @param $value mixed
     *            Property value
     * @throws Exception Invalid model property
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method)) {
            throw new Exception('Invalid model property');
        }
        $this->$method($value);
    }

    /**
     * Magic get function, to enforce use of getters
     *
     * @param $name string
     *            Property name
     * @throws Exception Invalid model property
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method)) {
            throw new Exception('Invalid model property');
        }
        return $this->$method();
    }

    /**
     * Set multiple properties
     *
     * @param $options array
     *            Associative array of properties to set
     * @return My_Model
     */
    public function exchangeArray(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

}
