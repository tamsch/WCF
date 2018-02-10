<?php
declare(strict_types=1);
namespace wcf\system\form\builder\field;

/**
 * Provides default implementations of `IMinimumFormField` methods.
 * 
 * @author	Matthias Schmidt
 * @copyright	2001-2018 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\System\Form\Builder\Field
 * @since	3.2
 */
trait TMinimumFormField {
	/**
	 * minimum of the field value
	 * @var	null|int
	 */
	protected $__minimum;
	
	/**
	 * Returns the minimum of the values of this field or `null` if no minimum
	 * has been set.
	 * 
	 * @return	null|int
	 */
	public function getMinimum() {
		return $this->__minimum;
	}
	
	/**
	 * Sets the minimum of the values of this field. If `null` is passed, the
	 * minimum is removed.
	 * 
	 * @param	null|int	$minimum	minimum field value
	 * @return	IMinimumFormField		this field
	 * 
	 * @throws	\InvalidArgumentException	if the given minimum is no integer or otherwise invalid
	 */
	public function minimum(int $minimum = null): IMinimumFormField {
		if ($minimum !== null) {
			if (!is_int($minimum)) {
				throw new \InvalidArgumentException("Given minimum is no int, '" . gettype($minimum) . "' given.");
			}
			
			if ($this instanceof IMaximumFormField) {
				$maximum = $this->getMaximum();
				if ($maximum !== null && $minimum > $maximum) {
					throw new \InvalidArgumentException("Minimum ({$minimum}) cannot be greater than maximum ({$maximum}).");
				}
			}
		}
		
		$this->__minimum = $minimum;
		
		return $this;
	}
}
