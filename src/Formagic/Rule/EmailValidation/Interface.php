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
 * Defines rule validation strategy interface
 *
 * @package     Formagic\Rule\EmailValidation
 * @author      Florian Sonnenburg
 * @since       1.1.0 First time introduced
 **/
interface Formagic_Rule_EmailValidation_Interface
{
    /**
     * Check if mail address is valid
     *
     * @param string $mailAddress Mail address to be checked
     * @return boolean Validation result
     */
    public function isValidEmailAddress($mailAddress);
}
