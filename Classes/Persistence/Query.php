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
class Tx_Nxsolrbackend_Persistence_Query extends Tx_Extbase_Persistence_Query implements Tx_Nxsolrbackend_Persistence_QueryInterface {
	
	/**
	 * @var Tx_Nxsolrbackend_Persistence_FacetConfiguration
	 */
	protected $facetConfiguration;

	/**
	 * Returns the additional facet configuration, if exists.
	 *
	 * @return Tx_Nxsolrbackend_Persistence_FacetConfiguration facetConfiguration object
	 */
	public function getFacetConfiguration() {
		return $this->facetConfiguration;
	}

	/**
	 * Sets additional facet configuration
	 *
	 * @param Tx_Nxsolrbackend_Persistence_FacetConfiguration facet configuration object
	 */
	public function setFacetConfiguration(Tx_Nxsolrbackend_Persistence_FacetConfiguration $facetConfiguration) {
		$this->facetConfiguration = $facetConfiguration;
	}
	
	/**
	 * Executes the query. In contrast to Extbase's Query->execute() a QueryResult object is returned even
	 * if returnRawQueryResult is set to TRUE. This is necessary as a plain array of rows cannot contain
	 * for example facets and other additional information. If you need an array of objects, use the
	 * QueryResultInterface::toArray() method.
	 * (non-PHPdoc)
	 * @see Classes/Persistence/Tx_Extbase_Persistence_Query#execute()
	 */
	public function execute() {
		return $this->objectManager->create('Tx_Nxsolrbackend_Persistence_QueryResultInterface', $this);
	}
	
	/**
	 * Returns a in range criterion used for matching objects against a query
	 *
	 * @param string $propertyName
	 * @param mixed $lowerBound
	 * @param mixed $upperBound
	 * @return Tx_Nxsolrbackend_Persistence_QOM_RangeInterface
	 */
	public function inRangeInclusive($propertyName, $lowerBound, $upperBound) {
		return $this->objectManager->create(
			'Tx_Nxsolrbackend_Persistence_QOM_InclusiveRange',
			$this->qomFactory->propertyValue($propertyName, $this->getSelectorName()),
			$lowerBound,
			$upperBound
		);
	}
	
	/**
	 * Returns a in range criterion used for matching objects against a query
	 *
	 * @param string $propertyName
	 * @param mixed $lowerBound
	 * @param mixed $upperBound
	 * @return Tx_Nxsolrbackend_Persistence_QOM_RangeInterface
	 */
	public function inRangeExclusive($propertyName, $lowerBound, $upperBound) {
		return $this->objectManager->create(
			'Tx_Nxsolrbackend_Persistence_QOM_ExclusiveRange',
			$this->qomFactory->propertyValue($propertyName, $this->getSelectorName()),
			$lowerBound,
			$upperBound
		);
	}
	

}
?>