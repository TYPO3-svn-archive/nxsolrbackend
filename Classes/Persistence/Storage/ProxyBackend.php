<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Lienhart Woitok <lienhart.woitok@netlogix.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * A Storage backend that dynamically decides which storage backend to actually use for a node type
 *
 * @package Nxsolrbackend
 * @subpackage Persistence\Storage
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Nxsolrbackend_Persistence_Storage_ProxyBackend implements Tx_Extbase_Persistence_Storage_BackendInterface, t3lib_Singleton {

	/**
	 * Array of table names to proxy to a different backend
	 * @var array
	 */
	protected $tableNames = array();
	
	/**
	 * @var Tx_Extbase_Persistence_DataMapper
	 */
	protected $dataMapper;
	
	/**
	 * @var Tx_Extbase_Persistence_Storage_Typo3DbBackend
	 */
	protected $typo3DbBackend;
	
	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;
	
	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}
	
	/**
	 * Injects the DataMapper to map nodes to objects
	 *
	 * @param Tx_Extbase_Persistence_Mapper_DataMapper $dataMapper
	 * @return void
	 */
	public function injectDataMapper(Tx_Extbase_Persistence_Mapper_DataMapper $dataMapper) {
		$this->dataMapper = $dataMapper;
	}
	
	/**
	 * Inject the default Typo3DBBackend class
	 *
	 * @param Tx_Extbase_Persistence_Storage_Typo3DbBackend $typo3Backend
	 * @return void
	 */
	public function injectTypo3Backend(Tx_Extbase_Persistence_Storage_Typo3DbBackend $typo3Backend) {
		$this->typo3DbBackend = $typo3Backend;
	}
	
	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initialize the object. Decide which Solr server to use.
	 * @return void
	 */
	public function initializeObject() {
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		if (isset($frameworkConfiguration['persistence']['proxy'])) {
			$settings = $frameworkConfiguration['persistence']['proxy'];
		}
		
		if (isset($settings['classes']) && is_array($settings['classes'])) {
			foreach ($settings['classes'] as $className => $backendClassName) {
				if (class_exists($className) && class_exists($backendClassName)) {
					$tableName = $this->dataMapper->getDataMap($className)->getTableName();
					$this->tableNames[$tableName] = $backendClassName;
				}
			}
		}
	}
	
	/**
	 * Adds a row to the storage
	 *
	 * @param string $tableName The database table name
	 * @param array $row The row to insert
	 * @param boolean $isRelation TRUE if we are currently inserting into a relation table, FALSE by default
	 * @return void
	 */
	public function addRow($tableName, array $row, $isRelation = FALSE) {
		return $this->typo3DbBackend->addRow($tableName, $row, $isRelation);
	}

	/**
	 * Updates a row in the storage
	 *
	 * @param string $tableName The database table name
	 * @param array $row The row to update
	 * @param boolean $isRelation TRUE if we are currently inserting into a relation table, FALSE by default
	 * @return void
	 */
	public function updateRow($tableName, array $row, $isRelation = FALSE) {
		return $this->typo3DbBackend->updateRow($tableName, $row, $isRelation);
	}

	/**
	 * Deletes a row in the storage
	 *
	 * @param string $tableName The database table name
	 * @param array $identifier An array of identifier array('fieldname' => value). This array will be transformed to a WHERE clause
	 * @param boolean $isRelation TRUE if we are currently inserting into a relation table, FALSE by default
	 * @return void
	 */
	public function removeRow($tableName, array $identifier, $isRelation = FALSE) {
		return $this->typo3DbBackend->removeRow($tableName, $identifier, $isRelation);
	}

	/**
	 * Returns an array with rows matching the query.
	 *
	 * @param Tx_Extbase_Persistence_QOM_QueryObjectModelInterface $query
	 * @return array
	 */
	public function getRows(Tx_Extbase_Persistence_QOM_QueryObjectModelInterface $query) {
		return $this->resolveStorageBackend($query)->getRows($query);
	}

	/**
	 * Returns the number of tuples matching the query.
	 *
	 * @param Tx_Extbase_Persistence_QOM_QueryObjectModelInterface $query
	 * @return int The number of matching tuples
	 */
	public function countRows(Tx_Extbase_Persistence_QOM_QueryObjectModelInterface $query) {
		return $this->resolveStorageBackend($query)->countRows($query);
	}
	
		/**
	 * Returns the number of items matching the query.
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return integer
	 */
	public function getObjectCountByQuery(Tx_Extbase_Persistence_QueryInterface $query) {
		return $this->resolveStorageBackend($query)->getObjectCountByQuery($query);
	}
	
	/**
	 * Returns the object data matching the $query.
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return array
	 */
	public function getObjectDataByQuery(Tx_Extbase_Persistence_QueryInterface $query) {
		return $this->resolveStorageBackend($query)->getObjectDataByQuery($query);
	}
	
	/**
	 * Checks if a Value Object equal to the given Object exists in the data base
	 *
	 * @param Tx_Extbase_DomainObject_AbstractValueObject $object The Value Object
	 * @return array The matching uid
	 */
	public function getUidOfAlreadyPersistedValueObject(Tx_Extbase_DomainObject_AbstractValueObject $object) {
		return 	$this->typo3DbBackend->getUidOfAlreadyPersistedValueObject($object);
	}
	
	/**
	 * Resolve the storage backend to use for a given query
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return Tx_Extbase_Persistence_Storage_BackendInterface
	 */
	protected function resolveStorageBackend(Tx_Extbase_Persistence_QueryInterface $query) {
		$source = $query->getSource();
		if ($source instanceof Tx_Extbase_Persistence_QOM_SelectorInterface) {
			$tableName = $source->getSelectorName();
		} elseif ($source instanceof Tx_Extbase_Persistence_QOM_JoinInterface) {
			$leftSource = $source->getLeft();
			$tableName = $leftSource->getSelectorName();
		}
		
		if (isset($this->tableNames[$tableName])) {
			return $this->objectManager->get($this->tableNames[$tableName]);
		} else {
			return $this->typo3DbBackend;
		}
	}
}
?>