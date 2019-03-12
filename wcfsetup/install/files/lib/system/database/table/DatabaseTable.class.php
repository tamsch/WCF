<?php
namespace wcf\system\database\table;
use wcf\system\application\ApplicationHandler;
use wcf\system\database\editor\DatabaseEditor;
use wcf\system\database\editor\MySQLDatabaseEditor;
use wcf\system\exception\NotImplementedException;

/**
 * Creates, modifies and drops database tables by computing the difference between the current
 * database structure and the intended layout.
 * 
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package     WoltLabSuite\Core\System\Database\Table
 */
class DatabaseTable {
	/** @var DatabaseTableColumn[] */
	protected $columns = [];
	protected $createIfNotExists = false;
	protected $indices = [];
	protected $tableName = '';
	
	public function __construct($tableName, $createIfNotExists) {
		$this->tableName = ApplicationHandler::insertRealDatabaseTableNames($tableName);
		$this->createIfNotExists = $createIfNotExists;
	}
	
	/**
	 * @param DatabaseTableColumn[] $columns
	 */
	public function columns(array $columns) {
		foreach ($columns as $column) {
			if (!($column instanceof DatabaseTableColumn)) {
				throw new \LogicException('Expected an instance of `DatabaseTableColumn`.');
			}
			
			$this->columns[$column->getName()] = $column;
		}
	}
	
	public function indices(array $indices) {
		throw new NotImplementedException();
	}
	
	public function commit(MySQLDatabaseEditor $editor) {
		$create = false;
		if ($this->createIfNotExists && in_array($this->tableName, $editor->getTableNames())) {
			$create = true;
		}
		
		// TODO: `filterColumns()` should always be used, but accept a second parameter for the table creation.
		if (!$create) {
			$columns = $this->filterColumns($editor->getColumns($this->tableName));
			wcfDebug($columns);
		}
		
		// The database already uses the same structure.
		// TODO: Include the indices.
		if (empty($columns)) {
			return;
		}
		
		// TODO: Apply and log the changes.
		throw new NotImplementedException();
	}
	
	/**
	 * @param string[][][] $existingColumnData
	 * @return DatabaseTableColumn[][]
	 */
	protected function filterColumns(array $existingColumnData) {
		$columns = ['add' => [], 'drop' => [], 'modify' => []];
		
		foreach ($this->columns as $columnName => $column) {
			$createColumn = true;
			foreach ($existingColumnData as $columnData) {
				if ($columnData['name'] === $columnName) {
					$createColumn = false;
					
					if ($column->getDropColumn()) {
						$columns['drop'][] = $column;
						continue 2;
					}
					
					if (empty($column->getMismatches($columnData['data']))) {
						$columns['modify'][] = $column;
					}
					
					break;
				}
			}
			
			if ($createColumn) {
				$columns['add'][] = $column;
			}
		}
		
		return $columns;
	}
}
