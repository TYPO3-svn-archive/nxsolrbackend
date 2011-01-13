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
 * A Storage backend
 *
 * @package Nxsolrbackend
 * @subpackage Persistence\Storage
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @access private
 */
class Tx_Nxsolrbackend_Persistence_Storage_SolrBackend implements Tx_Nxsolrbackend_Persistence_Storage_SolrBackendInterface, t3lib_Singleton {

	/**
	 * The solr communication object
	 *
	 * @var Tx_Nxsolrbackend_Persistence_Storage_SolrConnection
	 */
	protected $solrConnection;
	
	/**
	 * @var Tx_Extbase_Persistence_DataMapper
	 */
	protected $dataMapper;
	
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
	 * Inject the solr connection
	 *
	 * @param Tx_Nxsolrbackend_Persistence_Storage_SolrConnection $solrConnection
	 * @return void
	 */
	public function injectSolrConnection(Tx_Nxsolrbackend_Persistence_Storage_SolrConnection $solrConnection) {
		$this->solrConnection = $solrConnection;
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
		throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Nobody has yet implemented Tx_Nxsolrbackend_Persistence_Storage_SolrBackend->addRow()', 1265281849);
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
		throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Nobody has yet implemented Tx_Nxsolrbackend_Persistence_Storage_SolrBackend->updateRow()', 1265281850);
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
		throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Nobody has yet implemented Tx_Nxsolrbackend_Persistence_Storage_SolrBackend->removeRow()', 1265281851);
	}

	/**
	 * Returns the object data matching the $query.
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return array
	 */
	public function getObjectDataByQuery(Tx_Extbase_Persistence_QueryInterface $query) {
		$statement = $query->getStatement();
		if($statement instanceof Tx_Extbase_Persistence_QOM_Statement) {
			//TODO: parse and execute a plain solr request
			throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Nobody has yet implemented Tx_Nxsolrbackend_Persistence_Storage_SolrBackend->getObjectDataByQuery() for use with statement', 1265281852);
		} else {
			$parameters = array();
			$request = $this->getStatement($query, $parameters);
			
		}
		$this->replacePlaceholders($request, $parameters);
			//print_r($request);
		$result = $this->solrConnection->query($request);

		if (is_array($result['response']['docs'])) {
			return $result['response']['docs'];
		} else {
			return array();
		}
	}
	
	/**
	 * Returns the object and facet data matching the $query.
	 *
	 * @param Tx_Nxsolrbackend_Persistence_QueryInterface $query
	 * @return array
	 */
	public function getObjectAndFacetDataByQuery(Tx_Nxsolrbackend_Persistence_QueryInterface $query) {
		$parameters = array();

		$statement = $query->getStatement();
		if ($statement instanceof Tx_Extbase_Persistence_QOM_Statement) {
			//TODO: parse and execute a plain solr request
			throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Nobody has yet implemented Tx_Nxsolrbackend_Persistence_Storage_SolrBackend->getObjectAndFacetDataByQuery() for plain statements', 1293030886);
		} else {
			$parameters = array();
			$request = $this->getStatement($query, $parameters);
			
		}
		$this->replacePlaceholders($request, $parameters);
			//print_r($request);
		$result = $this->solrConnection->query($request);
		
		if (!is_array($result['response']['docs'])) {
			$result['response']['docs'] = array();
		}
		if (!is_array($result['facet_counts'])) {
			$result['facet_counts'] = array();
		}
		
		return $result;
	}
	
	/**
	 * Returns the number of items matching the query.
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return integer
	 */
	public function getObjectCountByQuery(Tx_Extbase_Persistence_QueryInterface $query) {
		
		$parameters = array();
		$statementParts = $this->parseQuery($query, $parameters);
		$statementParts['limit'] = '&rows=0';
		$statement = $this->buildStatement($statementParts);
		
		$this->replacePlaceholders($statement, $parameters);
		
		$result = $this->solrConnection->query($statement);
		return intval($result['response']['numFound']);
	}
	
	/**
	 * Returns the statement, ready to be executed.
	 *
	 * @param Tx_Extbase_Persistence_QOM_QueryObjectModelInterface $query
	 * @return string The SQL statement
	 */
	protected function getStatement(Tx_Extbase_Persistence_QueryInterface $query, array &$parameters) {
		$statementParts = $this->parseQuery($query, $parameters);
		$statement = $this->buildStatement($statementParts);
		return $statement;
	}
	
	/**
	 * Returns the statement, ready to be executed.
	 *
	 * @param array $statementParts The SQL statement parts
	 * @return string The SQL statement
	 */
	public function buildStatement(array $statementParts) {
		$statement = '';
		if (!empty($statementParts['where'])) {
			$statement .= '&q=' . implode('', $statementParts['where']);
			
			// TODO implement support to map additionalWhereClause to filter query (fq)
//			if (!empty($solr['additionalWhereClause'])) {
//				$statement .= ' AND ' . implode(' AND ', $solr['additionalWhereClause']);
//			}
//		} elseif (!empty($solr['additionalWhereClause'])) {
//			$statement .= ' WHERE ' . implode(' AND ', $solr['additionalWhereClause']);
		} else {
			$statement .= '&q=*:*';
		}
		if (!empty($statementParts['orderings'])) {
			$statement .= '&sort=' . implode(',', $statementParts['orderings']);
		}
		if (!empty($statementParts['limit'])) {
			$statement .= $statementParts['limit'];
		} else {
			$statement .= '&rows=999999';
		}
		
		if (!empty($statementParts['facet'])) {
			$statement .= '&' . implode('&', $statementParts['facet']);
		}
		
		return $statement;
	}
	
	/**
	 * Parses the query and returns the statement parts.
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @return array The statement parts
	 */
	protected function parseQuery(Tx_Extbase_Persistence_QueryInterface $query, array &$parameters) {
		$statementParts = array();
		$statementParts['fields'] = array();
		$statementParts['where'] = array();
		$statementParts['facet'] = array();
		$statementParts['additionalWhereClause'] = array();
		$statementParts['orderings'] = array();
		$statementParts['limit'] = array();

		$source = $query->getSource();
		
		$this->parseConstraint($query->getConstraint(), $source, $statementParts, $parameters);
		$this->parseOrder($query->getOrderings(), $source, $statementParts);
		$this->parseLimitAndOffset($query->getLimit(), $query->getOffset(), $statementParts);
		
		if($query instanceof Tx_Nxsolrbackend_Persistence_QueryInterface && $query->getFacetConfiguration() !== NULL) {
			$this->parseFacet($query->getFacetConfiguration(), $statementParts);
		}
		
		return $statementParts;
	}

	/**
	 * Transforms a Query Facet into solr statement parts
	 *
	 * @param Tx_Nxsolrbackend_Persistence_FacetConfiguration $facetConfiguration
	 * @param array $statementParts
	 * @return void
	 */
	protected function parseFacet(Tx_Nxsolrbackend_Persistence_FacetConfiguration $facetConfiguration, array &$statementParts) {
		$statementParts['facet'][] = 'facet=on';
		
		$facetFields = $facetConfiguration->getFields();
		if (!empty($facetFields)) {
			foreach ($facetFields as $field) {
				$statementParts['facet'][] = 'facet.field=' . urlencode($field);
			}
			
			if ($facetConfiguration->getSortingType() !== NULL) {
				if ($facetConfiguration->getSortingType() !== 'count' && $facetConfiguration->getSortingType() !== 'lex') {
					throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_InvalidSortingType('The given sorting type ' . $facetConfiguration->getSortingType() . 'is not vaild.', 1293101989);
				}
				$statementParts['facet'][] = 'facet.sort=' . $facetConfiguration->getSortingType();
			}
			if ($facetConfiguration->getLimit() !== NULL) {
				$statementParts['facet'][] = 'facet.limit=' . intval($facetConfiguration->getLimit());
			}
			if ($facetConfiguration->getOffset() !== NULL) {
				$statementParts['facet'][] = 'facet.offset=' . intval($facetConfigurationConfiguration->getOffset());
			}
			if ($facetConfiguration->getMinCount() !== NULL) {
				$statementParts['facet'][] = 'facet.mincount=' . intval($facetConfiguration->getMinCount());
			}
			if ($facetConfiguration->getCountMissing()) {
				$statementParts['facet'][] = 'facet.missing=true';
			}
			if ($facetConfiguration->getFilterPrefix() !== NULL) {
				$statementParts['facet'][] = 'facet.prefix=' . urlencode($facetConfiguration->getFilterPrefix());
			}
			if ($facetConfiguration->getMethod() !== NULL) {
				if ($facetConfiguration->getMethod() !== 'enum' && $facetConfiguration->getMethod() !== 'fc') {
					throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_InvalidMethod('The given method ' . $facetConfiguration->getMethod() . ' is not valid', 1293107085);
				}
				$statementParts['facet'][] = 'facet.method=' . $facetConfiguration->getMethod();
			}
		}
		
		$facetQueries = $facetConfiguration->getQueries();
		if (is_array($facetQueries) || $facetQueries instanceof Traversable) {
			foreach ($facetQueries as $facetQuery) {
				$statementParts['facet'][] = 'facet.query=' . urlencode($facetQuery);
			}
		}
	}
	
	/**
	 * Transforms a constraint into Solr query syntax and parameter arrays
	 *
	 * @param Tx_Extbase_Persistence_QOM_ConstraintInterface $constraint The constraint
	 * @param Tx_Extbase_Persistence_QOM_SourceInterface $source The source
	 * @param array &$statementParts The query parts
	 * @param array &$parameters The parameters that will replace the markers
	 * @return void
	 */
	protected function parseConstraint(Tx_Extbase_Persistence_QOM_ConstraintInterface $constraint = NULL, Tx_Extbase_Persistence_QOM_SourceInterface $source, array &$statementParts, array &$parameters) {
		if ($constraint instanceof Tx_Extbase_Persistence_QOM_AndInterface) {
			$statementParts['where'][] = '(';
			$this->parseConstraint($constraint->getConstraint1(), $source, $statementParts, $parameters);
			$statementParts['where'][] = urlencode(' AND ');
			$this->parseConstraint($constraint->getConstraint2(), $source, $statementParts, $parameters);
			$statementParts['where'][] = ')';
		} elseif ($constraint instanceof Tx_Extbase_Persistence_QOM_OrInterface) {
			$statementParts['where'][] = '(';
			$this->parseConstraint($constraint->getConstraint1(), $source, $statementParts, $parameters);
			$statementParts['where'][] = urlencode(' ');
			$this->parseConstraint($constraint->getConstraint2(), $source, $statementParts, $parameters);
			$statementParts['where'][] = ')';
		} elseif ($constraint instanceof Tx_Extbase_Persistence_QOM_NotInterface) {
			$statementParts['where'][] = 'NOT(';
			$this->parseConstraint($constraint->getConstraint(), $source, $statementParts, $parameters);
			$statementParts['where'][] = ')';
		} elseif ($constraint instanceof Tx_Extbase_Persistence_QOM_ComparisonInterface) {
			$this->parseComparison($constraint, $source, $statementParts, $parameters);
		} elseif ($constraint instanceof Tx_Nxsolrbackend_Persistence_QOM_RangeInterface) {
			$this->parseRange($constraint, $source, $statementParts, $parameters);
		}
	}
	
	/**
	 * Parse a Comparison into Solr query syntax and parameter arrays.
	 *
	 * @param Tx_Extbase_Persistence_QOM_ComparisonInterface $comparison The comparison to parse
	 * @param Tx_Extbase_Persistence_QOM_SourceInterface $source The source
	 * @param array &$statementParts SQL query parts to add to
	 * @param array &$parameters Parameters to bind to the SQL
	 * @return void
	 */
	protected function parseComparison(Tx_Extbase_Persistence_QOM_ComparisonInterface $comparison, Tx_Extbase_Persistence_QOM_SourceInterface $source, array &$statementParts, array &$parameters) {
		$operand1 = $comparison->getOperand1();
		$operator = $comparison->getOperator();
		$operand2 = $comparison->getOperand2();
		if (($operator === Tx_Extbase_Persistence_QueryInterface::OPERATOR_EQUAL_TO) && (is_array($operand2) || ($operand2 instanceof ArrayAccess) || ($operand2 instanceof Traversable))) {
			// FIXME this else branch enables equals() to behave like in(). This behavior is deprecated and will be removed in future. Use in() instead.
			$operator = Tx_Extbase_Persistence_QueryInterface::OPERATOR_IN;
		}
				
		if ($operator === Tx_Extbase_Persistence_QueryInterface::OPERATOR_IN) {
			$items = array();
			$hasValue = FALSE;
			foreach ($operand2 as $value) {
				$value = $this->getPlainValue($value);
				if ($value !== NULL) {
					$items[] = $value;
					$hasValue = TRUE;
				}
			}
			if ($hasValue === FALSE) {
				$statementParts['where'][] = '1<>1';
			} else {
				$this->parseDynamicOperand($operand1, $operator, $source, $statementParts, $parameters);
				$parameters[] = $items;
			}
		} elseif ($operator === Tx_Extbase_Persistence_QueryInterface::OPERATOR_CONTAINS) {
			throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Contains querys are not yet implemented, sorry.', 1293111337);
		} else {
			if ($operand2 === NULL) {
				throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Querys checking for (not) NULL values are not yet implemented, sorry.', 1293111338);
			}
			$this->parseDynamicOperand($operand1, $operator, $source, $statementParts, $parameters);
			$parameters[] = $this->getPlainValue($operand2);
		}
	}
	
	/**
	 * Parse a range constraint into Solr query syntax and parameter arrays.
	 *
	 * @param Tx_Nxsolrbackend_Persistence_QOM_RangeInterface $constraint The comparison to parse
	 * @param Tx_Extbase_Persistence_QOM_SourceInterface $source The source
	 * @param array &$statementParts SQL query parts to add to
	 * @param array &$parameters Parameters to bind to the SQL
	 * @return void
	 */
	protected function parseRange(Tx_Nxsolrbackend_Persistence_QOM_RangeInterface $constraint, Tx_Extbase_Persistence_QOM_SourceInterface $source, array &$statementParts, array &$parameters) {
		$operand = $constraint->getOperand();
		$lowerBound = $constraint->getLowerBoundOperand();
		$upperBound = $constraint->getUpperBoundOperand();
		$columnName = $operand->getPropertyName();
		
		if ($constraint instanceof Tx_Nxsolrbackend_Persistence_QOM_InclusiveRange) {
			$rangeSpecification = '[?' . urlencode(' TO ') . '?]';
		} elseif ($constraint instanceof Tx_Nxsolrbackend_Persistence_QOM_ExclusiveRange) {
			$rangeSpecification = '{?' . urlencode(' TO ') . '?}';
		} else {
			throw new Tx_Nxsolrbackend_Persistence_Storage_Exception('Unknown range type.', 1293121560);
		}
		$statementParts['where'][] = $columnName . ':' . $rangeSpecification;
		$parameters[] = $this->getPlainValue($lowerBound);
		$parameters[] = $this->getPlainValue($upperBound);
	}
	
	/**
	 * Returns a plain value, i.e. objects are flattened out if possible.
	 *
	 * @param mixed $input
	 * @return mixed
	 */
	protected function getPlainValue($input) {
		if (is_array($input)) {
			throw new Tx_Extbase_Persistence_Exception_UnexpectedTypeException('An array could not be converted to a plain value.', 1274799932);
		}
		if ($input instanceof DateTime) {
			return $input->format('U');
		} elseif (is_object($input)) {
			if ($input instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
				return $input->getUid();
			} else {
				throw new Tx_Extbase_Persistence_Exception_UnexpectedTypeException('An object of class "' . get_class($input) . '" could not be converted to a plain value.', 1274799934);
			}
		} elseif (is_bool($input)) {
			return $input === TRUE ? 1 : 0;
		} else {
			return $input;
		}
	}
	
	/**
	 * Parse a DynamicOperand into Solr query syntax and parameter arrays.
	 *
	 * @param Tx_Extbase_Persistence_QOM_DynamicOperandInterface $operand
	 * @param string $operator One of the JCR_OPERATOR_* constants
	 * @param Tx_Extbase_Persistence_QOM_SourceInterface $source The source
	 * @param array &$statementParts The query parts
	 * @param array &$parameters The parameters that will replace the markers
	 * @return void
	 */
	protected function parseDynamicOperand(Tx_Extbase_Persistence_QOM_DynamicOperandInterface $operand, $operator, Tx_Extbase_Persistence_QOM_SourceInterface $source, array &$statementParts, array &$parameters) {
		if ($operand instanceof Tx_Extbase_Persistence_QOM_PropertyValueInterface) {
			$columnName = urlencode($operand->getPropertyName());
			$operator = $this->resolveOperator($operator);
			
			$constraint = $columnName . ':' . $operator;

			$statementParts['where'][] = $constraint;
		}
	}
	
	
	/**
	 * Returns the Solr operator for the given JCR operator type.
	 *
	 * @param string $operator One of the JCR_OPERATOR_* constants
	 * @return string an SQL operator
	 */
	protected function resolveOperator($operator) {
		switch ($operator) {
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_IN:
				$operator = '?';
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_EQUAL_TO:
				$operator = '?';
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_NOT_EQUAL_TO:
				throw new Tx_Nxsolrbackend_Persistence_Storage_Exception_UnimplementedFeature('Operator not eqaul not supported. Use conjuntion of not and equals operator instead.', 1293113922);
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_LESS_THAN:
				$operator = urlencode('{* TO ').'?}';
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_LESS_THAN_OR_EQUAL_TO:
				$operator = urlencode('[* TO ').'?]';
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_GREATER_THAN:
				$operator = '{?' . urlencode(' TO *}');
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_GREATER_THAN_OR_EQUAL_TO:
				$operator = '[?' . urlencode(' TO *]');
				break;
			case Tx_Extbase_Persistence_QueryInterface::OPERATOR_LIKE:
				$operator = '?';
				break;
			default:
				throw new Tx_Extbase_Persistence_Exception('Unsupported operator encountered.', 1242816073);
		}

		return $operator;
	}
	
	/**
	 * Transforms orderings into Solr syntax.
	 *
	 * @param array $orderings Ann array of orderings (Tx_Extbase_Persistence_QOM_Ordering)
	 * @param Tx_Extbase_Persistence_QOM_SourceInterface $source The source
	 * @param array &$statementParts The query parts
	 * @return void
	 */
	protected function parseOrder(array $orderings, Tx_Extbase_Persistence_QOM_SourceInterface $source, array &$statementParts) {
		foreach ($orderings as $propertyName => $order) {
			switch ($order) {
				case Tx_Extbase_Persistence_QOM_QueryObjectModelConstantsInterface::JCR_ORDER_ASCENDING: // Deprecated since Extbase 1.1
				case Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING:
					$order = 'ASC';
					break;
				case Tx_Extbase_Persistence_QOM_QueryObjectModelConstantsInterface::JCR_ORDER_DESCENDING: // Deprecated since Extbase 1.1
				case Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING:
					$order = 'DESC';
					break;
				default:
					throw new Tx_Extbase_Persistence_Exception_UnsupportedOrder('Unsupported order encountered.', 1242816074);
			}
			
			$statementParts['orderings'][] = urlencode($propertyName . ' ' . strtolower($order));
		}
	}
	
	/**
	 * Transforms limit and offset into Solr syntax
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param array &$statementParts
	 * @return void
	 */
	protected function parseLimitAndOffset($limit, $offset, array &$statementParts) {
		if ($limit !== NULL && $offset !== NULL) {
			$statementParts['limit'] = '&start=' . $offset . '&rows=' . $limit;
		} elseif ($limit !== NULL) {
			$statementParts['limit'] = '&rows=' . $limit;
		}
	}
	
	/**
	 * Replace query placeholders in a query part by the given
	 * parameters.
	 *
	 * @param string $statementString The query part with placeholders
	 * @param array $parameters The parameters
	 * @return string The query part with replaced placeholders
	 */
	protected function replacePlaceholders(&$statementString, array $parameters) {
		// TODO profile this method again
		if (substr_count($statementString, '?') !== count($parameters)) {
			throw new Tx_Extbase_Persistence_Exception('The number of question marks to replace must be equal to the number of parameters.', 1242816074);
		}
		
		$offset = 0;
		foreach ($parameters as $parameter) {
			$markPosition = strpos($statementString, '?', $offset);
			if ($markPosition !== FALSE) {
				if ($parameter === NULL) {
					$parameter = 'NULL';
				} elseif (is_array($parameter)) {
					$items = array();
					foreach ($parameter as $item) {
						$items[] = $this->escapeParameter($item);
					}
					$parameter = '(' . implode(' ', $items) . ')';
				}
				else {
					$parameter = $this->escapeParameter($parameter);
				}
				
				$statementString = substr($statementString, 0, $markPosition) . urlencode($parameter) . substr($statementString, $markPosition + 1);
			}
			$offset = $markPosition + strlen($parameter);
		}
	}
	
	/**
	 * Escape all characters that may not appear literally in a solr query
	 *
	 * @param string $parameter
	 * @return string
	 */
	protected function escapeParameter($parameter) {
		return str_replace(
			array(
				'\\',
				'+',
				'-',
				'&&',
				'||',
				'!',
				'(',
				')',
				'{',
				'}',
				'[',
				']',
				'^',
				'"',
				'~',
				'*',
				'?',
				':',
				' '
			),
			array(
				'\\\\',
				'\+',
				'\-',
				'\&&',
				'\||',
				'\!',
				'\(',
				'\)',
				'\{',
				'\}',
				'\[',
				'\]',
				'\^',
				'\"',
				'\~',
				'\*',
				'\?',
				'\:',
				'\ '
			),
			$parameter
		);
	}
	
}
?>