<?php

/**
 * @see Zend_Form_Decorator_Abstract
 */
require_once 'Zend/Form/Decorator/Abstract.php';

class My_Form_Decorator_ControlGroup extends Zend_Form_Decorator_Abstract
{

    public function render($content)
    {
        if (count($this->getElement()->getMessages()) > 0) {
            $open = '<div class="control-group error">';
        } else {
            $open = '<div class="control-group">';
        }
        return $open . $content . '</div>';
    }

}
