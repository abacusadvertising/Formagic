<?php
/**
 * Formagic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at
 * http://www.formagic-php.net/license-agreement/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@formagic-php.net so we can send you a copy immediately.
 *
 * @author      Florian Sonnenburg
 * @copyright   2007-2014 Florian Sonnenburg
 * @license     http://www.formagic-php.net/license-agreement/   New BSD License
 */

/**
 * Formagic_Item_Upload
 *
 * @package     Formagic\Item
 * @author      Florian Sonnenburg
 * @since       1.0.0 First time introduced
 */
class Formagic_Item_Upload extends Formagic_Item_Abstract
{
    /**
     * Item type
     * @var string
     */
    protected $type = 'upload';

    /**
     * HTML string representation of upload input field
     *
     * @return string
     */
    public function getHtml()
    {
        $val = $this->getValue();
        if ($this->_isReadonly) {
            $str = '<div id=' . $this->getAttribute('id') . '>' . $val . '</div>';
        } else {
            $attributes = $this->getAttributes();
            $attributes['type'] = 'file';
            $attributes['value'] = $val;

            $str = '<input' . $this->_buildAttributeStr($attributes) . ' />';
        }
        return $str;
    }

    /**
     * Overwrites the superclass method Formagic_Item_Abstract::setValue().
     *
     * Sets the name of the uploaded file as the current item value.
     *
     * Implements a fluent interface pattern.
     *
     * @param mixed $value Will be ignored.
     *
     * @return Formagic_Item_Upload Fluent interface
     */
    public function setValue($value)
    {
        if (!empty($_FILES[$this->_name])) {
            $properties = $_FILES[$this->_name];
            $this->_value = new Formagic_Item_Value_UploadValue(
                $properties['name'],
                $properties['type'],
                $properties['tmp_name'],
                $properties['error'],
                $properties['size']
            );
        }

        return $this;
    }

}