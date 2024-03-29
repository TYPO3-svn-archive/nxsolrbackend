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
 * Stores the results of a query
 *
 * @package Nxsolrbackend
 * @subpackage Persistence
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
interface Tx_Nxsolrbackend_Persistence_QueryResultInterface extends Tx_Extbase_Persistence_QueryResultInterface {
	

	/**
	 * Returns the facet result if applicable.
	 *
	 * @return Tx_Nxsolrbackend_Persistence_FacetResultInterface
	 * @api
	 */
	public function getFacetResult();

	/**
	 * Check whether the query result contains additional facet information
	 *
	 * @return boolean
	 * @api
	 */
	public function hasFacet();

}
?>