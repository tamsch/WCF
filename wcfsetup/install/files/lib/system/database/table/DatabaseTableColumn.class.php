<?php
namespace wcf\system\database\table;
use wcf\system\exception\NotImplementedException;

/**
 * Wrapper for the primitive array structure that is used to express database table columns.
 * 
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package     WoltLabSuite\Core\System\Database\Table
 */
class DatabaseTableColumn {
	protected $autoIncrement = false;
	protected $columnName = '';
	protected $defaultValue = null;
	protected $dropColumn = false;
	protected $enumValues = '';
	protected $length = null;
	protected $notNull = false;
	protected $primaryKey = false;
	protected $type = '';
	
	/**
	 * @param string $columnName
	 * @param bool $dropColumn
	 */
	public function __construct($columnName, $dropColumn) {
		$this->columnName = $columnName;
		$this->dropColumn = $dropColumn;
	}
	
	/**
	 * @param bool $autoIncrement
	 * @return $this
	 */
	public function autoIncrement($autoIncrement) {
		$this->autoIncrement = $autoIncrement;
		
		// AUTO_INCREMENT columns can neither be NULL nor have a default value.
		$this->defaultValue = null;
		$this->notNull = true;
		
		return $this;
	}
	
	/**
	 * @param string|int|float|null $defaultValue
	 * @return $this
	 */
	public function defaultValue($defaultValue) {
		if ($this->autoIncrement) {
			throw new \LogicException('Columns with AUTO_INCREMENT cannot have a default value.');
		}
		
		$this->defaultValue = $defaultValue;
		
		return $this;
	}
	
	/**
	 * @param string[] $enumValues
	 * @return $this
	 */
	public function enumValues($enumValues) {
		$this->enumValues = $enumValues;
		
		return $this;
	}
	
	/**
	 * @param string|int|float $length
	 * @return $this
	 */
	public function length($length) {
		$this->length = $length;
		
		return $this;
	}
	
	/**
	 * @param bool $notNull
	 * @return $this
	 */
	public function notNull($notNull) {
		if ($this->autoIncrement && !$notNull) {
			throw new \LogicException('Columns with AUTO_INCREMENT cannot be NULL.');
		}
		
		$this->notNull = $notNull;
		
		return $this;
	}
	
	/**
	 * @param bool $primaryKey
	 * @return $this
	 */
	public function primaryKey($primaryKey) {
		$this->primaryKey = $primaryKey;
		
		return $this;
	}
	
	/**
	 * @param string $type
	 * @return $this
	 */
	public function type($type) {
		$this->type = $type;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->columnName;
	}
	
	public function getDropColumn() {
		return $this->dropColumn;
	}
	
	/**
	 * @return mixed[][]
	 */
	protected function getDefinition() {
		throw new NotImplementedException();
	}
	
	public function getKey() {
		return $this->primaryKey ? 'PRIMARY' : '';
	}
	
	/**
	 * @param mixed[] $definition
	 * @return mixed[][]
	 */
	public function getMismatches(array $definition) {
		$mismatches = [];
		
		foreach ($definition as $key => $value) {
			switch ($key) {
				case 'autoIncrement':
					if ($this->autoIncrement !== $value) {
						$mismatches[$key] = [$this->autoIncrement, $value];
					}
					break;
				
				case 'default':
					if ($this->defaultValue === null || $value === null) {
						if ($this->defaultValue !== $value) {
							$mismatches[$key] = [$this->defaultValue, $value];
						}
					}
					else if ($this->defaultValue != $value) {
						$mismatches[$key] = [$this->defaultValue, $value];
					}
					break;
					
				case 'key':
					if ($this->getKey() != $value) {
						$mismatches[$key] = [$this->getKey(), $value];
					}
					break;
					
				case 'length':
					if ($this->length != $value) {
						$mismatches[$key] = [$this->length, $value];
					}
					break;
					
				case 'notNull':
					if ($this->notNull != $value) {
						$mismatches[$key] = [$this->notNull, $value];
					}
					break;
				
				case 'type':
					if (strcasecmp($this->type, $value) !== 0) {
						$mismatches[$key] = [$this->type, $value];
					}
					break;
					
				default:
					throw new \RuntimeException("Unexpected field in comparison: '{$key}'.");
			}
		}
		
		return $mismatches;
	}
}
