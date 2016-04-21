<?php

namespace Engine;

/**
 * Station
 *
 * @author François LASSERRE
 * @copyright Copyright (c) 2016 All rights reserved.
 */
class Station
{
    private $name;
    private $rules;

    /**
     * __construct
     *
     * @param mixed $name
     * @param bool $default_rules
     * @access public
     * @return void
     */
    public function __construct($name, $rules)
    {
        $this->name = $name;
        $this->rules = $rules;
    }

    /**
     * getName
     *
     * @access public
     * @return void
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getRules
     *
     * @access public
     * @return void
     */
    public function getRules()
    {
        return $this->rules;
    }
}