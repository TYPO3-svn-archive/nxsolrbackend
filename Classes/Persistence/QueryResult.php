<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Lienhart Woitok <lienhart.woitok@netlogix.de>, netlogix
*
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
 *
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Nxsolrbackend_Persistence_QueryResult extends Tx_Extbase_Persistence_QueryResult implements Tx_Nxsolrbackend_Persistence_QueryResultInterface {
	
	
	/**
	 *
	 *
	 * @var Tx_Extbase_Persistence_FacetResultInterface
	 */
	protected $facetResult;
	
	/**
	 * Total number of results found
	 *
	 * @var integer
	 */
	protected $numberOfResults;
	
	/**
	 *
	 * @var Tx_Nxsolrbackend_Persistence_Storage_SolrBackendInterface
	 */
	protected $storageBackend;
	
	/**
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * Inject object manager
	 *
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}
	
	/**
	 * Inject persistence backend
	 *
	 * @param Tx_Nxsolrbackend_Persistence_Storage_SolrBackendInterface $persistenceBackend
	 * @return void
	 */
	public function injectStorageBackend(Tx_Nxsolrbackend_Persistence_Storage_SolrBackendInterface $storageBackend) {
		$this->storageBackend = $storageBackend;
	}
	
	protected function initialize() {
		if (!is_array($this->queryResult)) {
			$objectAndFacetData = $this->storageBackend->getObjectAndFacetDataByQuery($this->query);
			if ($this->query->getQuerySettings()->getReturnRawQueryResult() === TRUE) {
				$this->queryResult = $objectAndFacetData['response']['docs'];
			} else {
				$this->queryResult = $this->dataMapper->map($this->query->getType(), $objectAndFacetData['response']['docs']);
			}
			$this->facetResult = $this->objectManager->create('Tx_Nxsolrbackend_Persistence_FacetResultInterface', $objectAndFacetData['facet_counts']);
			$this->numberOfResults = $objectAndFacetData['response']['numFound'];
		}
	}

	/**
	 * Returns the additional facetObject, if exists
	 *
	 * @return Tx_Extbase_Persistence_FacetObject facetObject
	 */
	public function getFacetResult() {
		$this->initialize();
		// TODO throw exception if facet doesn't exist
		return $this->facetResult;
	}

	/**
	 * (non-PHPdoc)
	 * @see Classes/Persistence/Tx_Nxsolrbackend_Persistence_QueryResultInterface#hasFacet()
	 */
	public function hasFacet() {
		return $this->query->getFacet() !== NULL && $this->facetResult !== NULL;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Classes/Persistence/Tx_Extbase_Persistence_QueryResult#count()
	 */
	public function count() {
		if ($this->numberOfResults > 0) {
			return $this->numberOfResults;
		} else {
			return parent::count();
		}
	}
}
?>