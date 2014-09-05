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
 * @category    Formagic
 * @package     Item
 * @author      Florian Sonnenburg
 * @copyright   Copyright (c) 2007-2013 Florian Sonnenburg
 * @license     http://www.formagic-php.net/license-agreement/   New BSD License
 */

/**
 * Formagic_Item_Textarea
 *
 * @category    Formagic
 * @package     Item
 * @author      Florian Sonnenburg
 * @copyright   Copyright (c) 2009 Florian Sonnenburg
 */
class Formagic_Item_Textarea extends Formagic_Item_Abstract
{

    /**
     * HTML string representation of text input field
     *
     * @return string The HTML string
     */
    public function getHtml()
    {
        $val = htmlspecialchars($this->getValue());
        $attributes = $this->getAttributes();

        if ($this->_isReadonly) {
            $str = $val;
            $str .= '<input type="hidden" id="' . $attributes['id']
                . '" name="' . $attributes['name'] . '" value="' . $val . '" />';
        } else {
            $attributes = $this->getAttributes();
            if (!isset($attributes['rows'])) {
                $attributes['rows'] = 7;
            }
            if (!isset($attributes['cols'])) {
                $attributes['cols'] = 50;
            }
            $str = '<textarea' . $this->_buildAttributeStr($attributes) . '>'
                . $val . '</textarea>';
        }
        return $str;
    }

}