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
 * Formagic Container Item class
 *
 * Changes on following flag will be applied recursively to all items added to
 * the container object:
 *  - _isHidden
 *  - _isReadonly
 *  - _isDisabled
 *
 * Changes on the flag _isPostItem will only be applied to the container item
 * itself (and defaults to false as there is no use posting a container item).
 *
 * @package     Formagic\Item
 * @author      Florian Sonnenburg
 * @since       0.2.0 First time introduced
 */
class Formagic_Item_Container extends Formagic_Item_Abstract implements IteratorAggregate, Countable
{
    /**
     * Item type
     * @var string
     */
    protected $type = 'container';

    /**
     * Pointer to items array section of formagic object
     * @var Formagic_Item_Abstract[]
     **/
    protected $_items = array();

    /**
     * Container headline
     * @var string
     **/
    public $headline = '';

    /**
     * Define what attributes are required for default container item
     * @var array
     */
    protected $_requiredAttributes = array('id');

    /**
     * Adds formagic item object to array of items.
     *
     * Creates new item object and adds this, or adds passed formagicItem object
     *
     * @param Formagic_Item_Abstract|string $item String with item type or
     *        Formagic_Item_Abstract object
     * @param string $name String with item name. NULL if $type
     *        is Formagic_Item_Abstract object
     * @param array $args Array with additional item information. NULL if $type
     *        is Formagic_Item_Abstract object
     *
     * @throws Formagic_Exception if $item is no item object and $name is not given
     *
     * @return Formagic_Item_Container Fluent interface
     */
    public function addItem($item, $name = null, array $args = array())
    {
        if (!($item instanceOf Formagic_Item_Abstract)) {
            if (!$name) {
                throw new Formagic_Exception('Name string required for new items');
            }
            // hand down status flags to added items
            if ($this->_isHidden) {
                $args['hidden'] = true;
            }
            if ($this->_isReadonly) {
                $args['readonly'] = true;
            }
            if ($this->_isDisabled) {
                $args['disable'] = true;
            }
            $item = Formagic::createItem($item, $name, $args);
        }

        if ($this->_rules) {
            foreach ($this->_rules as $rule) {
                $item->addRule($rule);
            }
        }
        if ($this->_filters) {
            foreach ($this->_filters as $filter) {
                $item->addFilter($filter);
            }
        }

        $this->_items[$item->getName()] = $item;

        return $this;
    }

    /**
     * Counts items added to self and all sub-containers.
     *
     * @return integer The number of items in this container
     */
    public function count()
    {
        $count = 0;
        foreach($this->_items as $item) {
            if ($item instanceOf Formagic_Item_Container) {
                $count += $item->count();
            } else {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * Returns items array
     *
     * @return Formagic_Item_Abstract[]
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Returns added item $name.
     *
     * Throws Formagic_Exception by default if item does not exist. This can be
     * altered by setting $throwException to FALSE; the result value will be
     * NULL then.
     *
     * @param string $name Item identifier string.
     * @param boolean $throwException Returns NULL if set to FALSE. Please note that this parameter is deprecated
     *              since version 1.5.4. Please use {@link #hasItem hasItem()} to test if an item exists.
     *
     * @throws Formagic_Exception If item not found
     *
     * @return Formagic_Item_Abstract Returns the Formagic item identified by $name.
     */
    public function getItem($name, $throwException = true)
    {
        if (!$this->hasItem($name)) {
            if (!$throwException) {
                return null;
            }
            throw new Formagic_Exception("Item '$name' does not exist");
        }

        if (isset($this->_items[$name])) {
            $res = $this->_items[$name];
        } else {
            $res = null;
            foreach ($this->_items as $item) {
                if ($item instanceOf Formagic_Item_Container && $item->hasItem($name)) {
                    $res = $item->getItem($name);
                    break;
                }
            }
        }

        return $res;
    }

    /**
     * Checks if a specified item exists.
     *
     * Performs a deep check, on this container instance an all it's children.
     *
     * @param string $name Name of the item to be searched for.
     *
     * @return boolean Returns true if item exists
     */
    public function hasItem($name)
    {
        if (isset($this->_items[$name])) {
            $res = true;
        } else {
            $res = false;
            foreach ($this->_items as $item) {
                if ($item instanceOf Formagic_Item_Container && $item->hasItem($name)) {
                    $res = true;
                    break;
                }
            }
        }

        return $res;
    }

    /**
     * Sets new values for all items assigned to this container.
     *
     * Other than in Formagic_Item_Abstract defined, $value has to be an
     * associative array, the key being the name of the item the value belongs
     * to.
     *
     * Implements a fluent interface pattern.
     *
     * @param array|Formagic_Item_Value_ValueBag $value Set of new values for contained items.
     *
     * @throws Formagic_Exception if value is not an array
     * @return Formagic_Item_Container This object.
     */
    public function setValue($value)
    {
        // check that $value is array
        if (!is_array($value) && !($value instanceof Formagic_Item_Value_ValueBag)) {
            throw new Formagic_Exception('Container value has to be an array or Formagic_Item_Value_ValueBag');
        }

        $valuesArray = $value;
        if ($value instanceof Formagic_Item_Value_ValueBag) {
            $valuesArray = $value->getArrayCopy();
        }

        // set values to all registered items
        foreach ($this->_items as $item) {
            if ($item instanceOf Formagic_Item_Container) {
                // delegate to sub-containers
                $item->setValue($value);

            } elseif($item instanceOf Formagic_Item_ImageSubmit) {
                // special treatment for image type submit
                if (array_key_exists($item->getName() . '_x', $valuesArray)
                    && array_key_exists($item->getName() . '_y', $valuesArray)
                ) {
                    $clickCoordinates = array(
                        'x' => (int)$valuesArray[$item->getName() . '_x'],
                        'y' => (int)$valuesArray[$item->getName() . '_y']
                    );
                    $item->setClickCoordinates($clickCoordinates);
                    $item->setValue($item->getLabel());
                }

            } else {
                // everything else
                if (array_key_exists($item->getName(), $valuesArray)) {
                    $item->setValue($valuesArray[$item->getName()]);

                } else {
                    // no value submitted
                    if ($item instanceOf Formagic_Item_Checkbox) {

                        // if checkbox has no value submitted, it is not clicked --> set value to 0.
                        // this can only be done if in "submit mode"; if not, a formerly set default mode should not be
                        // overwritten
                        if (($value instanceof Formagic_Item_Value_ValueBag) && $value->isSubmitValueBag()) {
                            $item->setValue('');
                        }

                    } else {
                        // do not clear value if item has one set already
                        $itemValue = $item->getValue();
                        if (empty($itemValue)) {
                            $item->setValue(null);
                        }

                    }
                }
            }
        }
        return $this;
    }

    /**
     * Adds a rule to this container and all contained items.
     *
     * @see Formagic_Item_Abstract::addRule()
     * @param mixed $rule Rule type string or Formagic_Rule_Abstract object.
     * @param array $args Optional array of arguments. Will be passed to the
     *        rule constructor as array.
     * @throws Formagic_Exception if $rule argument is invalid
     * @return Formagic_Item_Container This object.
     */
    public function addRule($rule, array $args = array())
    {
        parent::addRule($rule, $args);
        foreach ($this->_items as $item) {
            $item->addRule($rule, $args);
        }
        return $this;
    }

    /**
     * Adds a filter to this container and all contained items.
     *
     * @see Formagic_Item_Abstract::addFilter()
     * @param mixed $filter Filter type string or Formagic_Filter_Interface object.
     * @param array $args Optional array of arguments. Will be passed to the
     *        filter constructor as array.
     * @throws Formagic_Exception if $filter argument is invalid
     * @return Formagic_Item_Container This object
     */
    public function addFilter($filter, array $args = null)
    {
        parent::addFilter($filter, $args);
        foreach ($this->_items as $item) {
            $item->addFilter($filter, $args);
        }
        return $this;
    }

    /**
     * Sets readonly flag to container item and all its descendants.
     *
     * Implements a fluent interface pattern.
     *
     * @param boolean $flag Readonly status flag.
     * @see Formagic_Item_Abstract::setReadonly()
     * @return Formagic_Item_Container This object.
     */
    public function setReadonly($flag)
    {
        $this->_isReadonly = $flag;
        foreach ($this->getItems() as $item) {
            $item->setReadonly($flag);
        }
        return $this;
    }

    /**
     * Sets hidden flag on container item and all its descendants.
     *
     * Implements a fluent interface pattern.
     *
     * @param boolean $flag Hidden status flag.
     * @see Formagic_Item_Abstract::setHidden()
     * @return Formagic_Item_Container This object.
     */
    public function setHidden($flag)
    {
        $this->_isHidden = $flag;
        foreach($this->getItems() as $item) {
            $item->setHidden($flag);
        }
        return $this;
    }

    /**
     * Disables container item and all its descendants and thus removes all
     * involved items from form.
     *
     * Implements a fluent interface pattern.
     *
     * @param boolean $flag Disabled status flag.
     * @see Formagic_Item_Abstract::setDisabled()
     * @return Formagic_Item_Container This object.
     */
    public function setDisabled($flag)
    {
        $this->_isDisabled = $flag;
        foreach($this->getItems() as $item) {
            $item->setDisabled($flag);
        }
        return $this;
    }

    /**
     * Returns values of all stored items.
     *
     * The return value will be an associative array, the keys being the name
     * of the item, the value being the filtered item value.
     *
     * @return array Array of item values.
     */
    public function getValue()
    {
        $res = array();
        foreach ($this->getItems() as $item) {
            if ($item->isIgnored()) {
                continue;
            }

            if ($item instanceOf Formagic_Item_Container) {
                /** @var array $value */
                $value = $item->getValue();
                $res = $res + $value;
            } else {
                $res[$item->getName()] = $item->getValue();
            }
        }
        return $res;
    }

    /**
     * Validates contained items.
     *
     * Iterates through all contained items. Disabled items are ignored for validation.
     *
     * If all items pass violation, validate() calls onValidate-handler and
     * returns its result.
     *
     * @return boolean
     */
    public function validate()
    {
        $valid = true;
        foreach ($this->_items as $item) {
            if ($item->isDisabled()) {
                continue;
            }
            if (!$item->validate()) {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Completes the IteratorAggregate interface.
     *
     * @return ArrayObject ArrayObject with container items.
     */
    public function getIterator()
    {
        $i = new ArrayIterator($this->_items);
        return $i;
    }
}
