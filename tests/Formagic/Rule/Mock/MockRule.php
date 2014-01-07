<?php
class Formagic_Rule_Mock_MockRule extends Formagic_Rule_Abstract
{
    public function validate($value)
    {
        if (!is_string($value)) {
            throw new Exception('Validation value is not a string');
        }
        return (bool)$value;
    }
}
