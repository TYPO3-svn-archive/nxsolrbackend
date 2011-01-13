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
 * A query object that provides additional functionallity to provide a facet configuration
 *
 * @package Nxsolrbackend
 * @subpackage Persistence
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
interface Tx_Nxsolrbackend_Persistence_QueryInterface extends Tx_Extbase_Persistence_QueryInterface {

	/**
	 * Returns the additional facet configuration, if exists.
	 *
	 * @return Tx_Nxsolrbackend_Persistence_FacetConfiguration facetConfiguration object
	 */
	public function getFacetConfiguration();

	/**
	 * Sets additional facet configuration
	 *
	 * @param Tx_Nxsolrbackend_Persistence_FacetConfiguration $facetConfiguration
	 * @api
	 */
	public function setFacetConfiguration(Tx_Nxsolrbackend_Persistence_FacetConfiguration $facetConfiguration);
	
	/**
	 * Returns a in range criterion used for matching objects against a query
	 *
	 * @param string $propertyName
	 * @param mixed $lowerBound
	 * @param mixed $upperBound
	 * @return Tx_Extbase_Persistence_QOM_ConstraintInterface
	 * @api
	 */
	public function inRangeInclusive($propertyName, $lowerBound, $upperBound);
	
	/**
	 * Returns a in range criterion used for matching objects against a query
	 *
	 * @param string $propertyName
	 * @param mixed $lowerBound
	 * @param mixed $upperBound
	 * @return Tx_Extbase_Persistence_QOM_ConstraintInterface
	 * @api
	 */
	public function inRangeExclusive($propertyName, $lowerBound, $upperBound);
	
}
?>