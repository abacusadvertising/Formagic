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
 * @copyright   Copyright (c) 2007-2014 Florian Sonnenburg
 * @license     http://www.formagic-php.net/license-agreement/   New BSD License
 */

/**
 * Checks if value is given
 *
 * @package     Formagic\Rule
 * @author      Florian Sonnenburg
 * @since       1.0.0 First time introduced
 **/
class Formagic_Rule_Regex extends Formagic_Rule_Abstract
{
    /**
     * The regex pattern
     * @var string
     */
    protected $_pattern;

    /**
     * Allows subclass initialization.
     *
     * @param array $arguments Array of arguments passed to __construct()
     *  Required key "pattern", containing the regex pattern
     * @throws Formagic_Exception If no regex pattern provided
     * @return void
     */
    protected function _init(array $arguments)
    {
        if (empty($arguments['pattern'])) {
            throw new Formagic_Exception('Regex rule needs a pattern');
        }
        $this->_pattern = $arguments['pattern'];
    }

    /**
     * Regex validation
     *
     * @param string $value Item to be checked
     * @return boolean True if the item value validated ok.
     **/
    public function validate($value)
    {
        if ($this->_isNoUserValue($value)) {
            return true;
        }

        $result = preg_match($this->_pattern, $value);
        return !empty($result);
    }

}