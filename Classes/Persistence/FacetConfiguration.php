<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Lienhart Woitok <lienhart.woitok@netlogix.de>
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
 * Configuration object that is used to configure the facets that should be returned by a query
 *
 * @package Nxsolrbackend
 * @subpackage Persistence
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_Nxsolrbackend_Persistence_FacetConfiguration {

	/**
	 * @var array
	 */
	protected $fields;

	/**
	 * @var string
	 */
	protected $sort;
	
	/**
	 * @var integer
	 */
	protected $limit;
	
	/**
	 * @var integer
	 */
	protected $offset;
	
	/**
	 * @var integer
	 */
	protected $minCount;
	
	/**
	 * @var boolean
	 */
	protected $missing;
	
	/**
	 * @var string
	 */
	protected $prefix;
	
	/**
	 * @var string
	 */
	protected $method;
	
	/**
	 * @var array
	 */
	protected $queries;
	
	
	/**
	 * Get the fields to facet on. Corresponds to the facet.field parameter.
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 * Sets the fields to facet on. Corresponds to the facet.field parameter.
	 *
	 * @param array $fields
	 * @api
	 */
	public function setFields($fields) {
		$this->fields = $fields;
	}
	
	/**
	 * Get the sorting order of facets. Can be either "count" or "lex". Corresponds to the facet.sort parameter.
	 *
	 * @return string
	 */
	public function getSortingType() {
		return $this->sort;
	}
	
	/**
	 * Set the sorting order of facets. Can be either "count" or "lex". Corresponds to the facet.sort parameter.
	 *
	 * @param string $sortingType
	 * @api
	 */
	public function setSortingType($sortingType) {
		$this->sort = $sortingType;
	}
	
	/**
	 * Get the maximum number of facet values. If -1, limit is disabled.
	 * Corresponds to facet.limit parameter.
	 *
	 * @return integer
	 */
	public function getLimit() {
		return $this->limit;
	}
	
	/**
	 * Set the maximum number of facet values. Set to -1 to disable this limit. Defaults to 100.
	 * Corresponds to facet.limit parameter.
	 *
	 * @param integer $limit
	 * @api
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}
	
	/**
	 * Get the offset after which the facet values are returned. Corresponds to facet.offset parameter.
	 *
	 * @return integer
	 */
	public function getOffset() {
		return $this->offset;
	}
	/**
	 * Set the offset after which the facet values are returned. Defaults to 0.
	 * Corresponds to facet.offset parameter.
	 *
	 * @param integer $offset
	 * @api
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
	}
	
	/**
	 * Get the minimum count of results a value must have to be returned. Corresponds to facet.mincount parameter.
	 *
	 * @return integer
	 */
	public function getMinCount() {
		return $this->minCount;
	}
	
	/**
	 * Set the minimum count of results a value must have to be returned. Defaults to 0.
	 * Corresponds to facet.mincount parameter.
	 *
	 * @param integer $minCount
	 * @api
	 */
	public function setMinCount($minCount) {
		$this->minCount = $minCount;
	}
	
	/**
	 * Get whether to add the count of all results not matching any value.
	 * Corresponds to facet.missing parameter
	 *
	 * @return boolean
	 */
	public function getCountMissing() {
		return $this->missing;
	}
	
	/**
	 * Set whether to add the count of all results not matching any value. Defaults to FALSE.
	 * Corresponds to facet.missing parameter
	 *
	 * @param boolean $countMissing
	 * @api
	 */
	public function setCountMissing($countMissing) {
		$this->missing = $countMissing;
	}
	
	/**
	 * Get the prefix that all values must start with in order to be returned.
	 * Corresponds to facet.prefix parameter
	 *
	 * @return string
	 */
	public function getFilterPrefix() {
		return $this->prefix;
	}
	
	/**
	 * Set the prefix that all values must start with in order to be returned.
	 * Corresponds to facet.prefix parameter
	 *
	 * @param string $prefix
	 * @api
	 */
	public function setFilterPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	/**
	 * Get the method to use for faceting. Corresponds to facet.method parameter
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}
	
	/**
	 * Set the method to use for faceting. Can be either "enum" or "fc".
	 * Corresponds to facet.method parameter
	 *
	 * @param string $method
	 * @api
	 */
	public function setMethod($method) {
		$this->method = $method;
	}
	
	/**
	 * Get the queries to facet on. Corresponds to facet.query parameter.
	 *
	 * @return array
	 */
	public function getQueries() {
		return $this->queries;
	}
	
	/**
	 * Set the queries to facet on. Corresponds to facet.query parameter.
	 *
	 * @param array $queries
	 * @api
	 */
	public function setQueries($queries) {
		$this->queries  = $queries;
	}
}
?>