<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Lienhart Woitok <lienhart.woitok@netlogix.de>, netlogix
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
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



class Tx_Nxsolrbackend_Persistence_Storage_SolrBackendTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	
	/**
	 * @var Tx_Nxsolrbackend_Persistence_Storage_SolrConnection
	 */
	protected $mockSolrConnection;
	
	public function setUp() {
		$this->mockSolrConnection = $this->getMock('Tx_Nxsolrbackend_Persistence_Storage_SolrConnection', array('query'));
	}
	
	public function tearDown() {
		$this->mockSolrConnection = NULL;
	}
	
	/**
	 * @test
	 */
	public function getObjectDataByQueryBuildsAndExecutesSolrQuery() {
		$mockQuery = $this->getMock('Tx_Extbase_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getStatement')->will($this->returnValue(NULL));
		
		$solrBackend = $this->getMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('getStatement', 'replacePlaceholders'));
		$solrBackend->expects($this->once())->method('getStatement')->will($this->returnValue('solr query'));
		
		$solrResponse = array('response' => array('docs' => array(array('id' => 1))));
		
		$this->mockSolrConnection->expects($this->once())->method('query')->with('solr query')->will($this->returnValue($solrResponse));
		$solrBackend->injectSolrConnection($this->mockSolrConnection);
		
		$objectData = $solrBackend->getObjectDataByQuery($mockQuery);
		$this->assertEquals($solrResponse['response']['docs'], $objectData);
	}
	
	/**
	 * @test
	 */
	public function getObjectAndFacetDataByQueryBuildsAndExecutesSolrQuery() {
		$mockQuery = $this->getMock('Tx_Nxsolrbackend_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getStatement')->will($this->returnValue(NULL));
		
		$solrBackend = $this->getMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('getStatement', 'replacePlaceholders'));
		$solrBackend->expects($this->once())->method('getStatement')->will($this->returnValue('solr query'));
		
		$solrResponse = array('response' => array('docs' => array(array('id' => 1))), 'facet_counts' => array());
		
		$this->mockSolrConnection->expects($this->once())->method('query')->with('solr query')->will($this->returnValue($solrResponse));
		$solrBackend->injectSolrConnection($this->mockSolrConnection);
		
		$objectData = $solrBackend->getObjectAndFacetDataByQuery($mockQuery);
		$this->assertEquals($solrResponse, $objectData);
	}
	
	/**
	 * @test
	 */
	public function getObjectCountByQueryBuildsAndExecutesSolrQuery() {
		$mockQuery = $this->getMock('Tx_Nxsolrbackend_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getStatement')->will($this->returnValue(NULL));
		
		$parsedQuery = array();
		$parsedQueryWithLimit = array('limit' => '&rows=0');
		
		$solrBackend = $this->getMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('parseQuery', 'buildStatement', 'replacePlaceholders'));
		$solrBackend->expects($this->once())->method('parseQuery')->will($this->returnValue($parsedQuery));
		$solrBackend->expects($this->once())->method('buildStatement')->with($parsedQueryWithLimit)->will($this->returnValue('solr query'));
		
		$solrResponse = array('response' => array('docs' => array(), 'numFound' => 42));
		
		$this->mockSolrConnection->expects($this->once())->method('query')->with('solr query')->will($this->returnValue($solrResponse));
		$solrBackend->injectSolrConnection($this->mockSolrConnection);
		
		$count = $solrBackend->getObjectCountByQuery($mockQuery);
		$this->assertEquals(42, $count);
	}
	
	/**
	 * @test
	 */
	public function parseQueryCreatesStatementParts() {
		$mockSource = $this->getMock('Tx_Extbase_Persistence_QOM_SelectorInterface');
		
		$mockConstraint = $this->getMock('Tx_Extbase_Persistence_QOM_ConstraintInterface');
		
		$mockOrderings = array('ordering' => 'desc');
		
		$mockQuery = $this->getMock('Tx_Extbase_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getSource')->will($this->returnValue($mockSource));
		$mockQuery->expects($this->any())->method('getConstraint')->will($this->returnValue($mockConstraint));
		$mockQuery->expects($this->any())->method('getOrderings')->will($this->returnValue($mockOrderings));
		$mockQuery->expects($this->any())->method('getLimit')->will($this->returnValue(42));
		$mockQuery->expects($this->any())->method('getOffset')->will($this->returnValue(23));
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('parseConstraint', 'parseOrder', 'parseLimitAndOffset'));
		$solrBackend->expects($this->once())->method('parseConstraint')->with($mockConstraint, $mockSource);
		$solrBackend->expects($this->once())->method('parseOrder')->with($mockOrderings, $mockSource);
		$solrBackend->expects($this->once())->method('parseLimitAndOffset')->with(42, 23);
		
		$statementParts = $solrBackend->_call('parseQuery', $mockQuery, array());
		
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $statementParts);
		$this->assertArrayHasKey('fields', $statementParts);
		$this->assertArrayHasKey('where', $statementParts);
		$this->assertArrayHasKey('facet', $statementParts);
		$this->assertArrayHasKey('additionalWhereClause', $statementParts);
		$this->assertArrayHasKey('orderings', $statementParts);
		$this->assertArrayHasKey('limit', $statementParts);
	}
	
	/**
	 * @test
	 */
	public function parseQueryParsesFacetIfFacetIsGiven() {
		$mockSource = $this->getMock('Tx_Extbase_Persistence_QOM_SelectorInterface');
		$mockConstraint = $this->getMock('Tx_Extbase_Persistence_QOM_ConstraintInterface');
		$mockOrderings = array('ordering' => 'desc');
		$mockFacetConfiguration = $this->getMock('Tx_Nxsolrbackend_Persistence_FacetConfiguration');
		
		$mockQuery = $this->getMock('Tx_Nxsolrbackend_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getSource')->will($this->returnValue($mockSource));
		$mockQuery->expects($this->any())->method('getConstraint')->will($this->returnValue($mockConstraint));
		$mockQuery->expects($this->any())->method('getOrderings')->will($this->returnValue($mockOrderings));
		$mockQuery->expects($this->any())->method('getLimit')->will($this->returnValue(42));
		$mockQuery->expects($this->any())->method('getOffset')->will($this->returnValue(23));
		$mockQuery->expects($this->any())->method('getFacetConfiguration')->will($this->returnValue($mockFacetConfiguration));
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('parseConstraint', 'parseOrder', 'parseLimitAndOffset', 'parseFacet'));
		$solrBackend->expects($this->once())->method('parseFacet')->with($mockFacetConfiguration);
		
		$solrBackend->_call('parseQuery', $mockQuery, array());
	}
	
	/**
	 * @test
	 */
	public function parseQueryDoesNotParseFacetIfNoFacetIsGiven() {
		$mockSource = $this->getMock('Tx_Extbase_Persistence_QOM_SelectorInterface');
		$mockConstraint = $this->getMock('Tx_Extbase_Persistence_QOM_ConstraintInterface');
		$mockOrderings = array('ordering' => 'desc');
		
		$mockQuery = $this->getMock('Tx_Nxsolrbackend_Persistence_Query', array(), array(), '', FALSE);
		$mockQuery->expects($this->any())->method('getSource')->will($this->returnValue($mockSource));
		$mockQuery->expects($this->any())->method('getConstraint')->will($this->returnValue($mockConstraint));
		$mockQuery->expects($this->any())->method('getOrderings')->will($this->returnValue($mockOrderings));
		$mockQuery->expects($this->any())->method('getLimit')->will($this->returnValue(42));
		$mockQuery->expects($this->any())->method('getOffset')->will($this->returnValue(23));
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('parseConstraint', 'parseOrder', 'parseLimitAndOffset', 'parseFacet'));
		$solrBackend->expects($this->never())->method('parseFacet');
		
		$solrBackend->_call('parseQuery', $mockQuery, array());
	}
	
	/**
	 * @test
	 */
	public function buildStatementCreatesQueryForAllObjectsIfNoWhereConditionWasGiven() {
		$mockStatementParts = array(
			'fields' => array(),
			'where' => array(),
			'facet' => array(),
			'additionalWhereClause' => array(),
			'orderings' => array(),
			'limit' => array(),
		);
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('dummy'));
		
		$statement = $solrBackend->buildStatement($mockStatementParts);
		$this->assertRegExp('/(?:^|&)q=\*:\*(?:$|&)/', $statement);
	}
	
	/**
	 * @test
	 */
	public function buildStatementCreatesQueryWithGivenCondition() {
		$mockStatementParts = array(
			'fields' => array(),
			'where' => array('foo:bar'),
			'facet' => array(),
			'additionalWhereClause' => array(),
			'orderings' => array(),
			'limit' => array(),
		);
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('dummy'));
		
		$statement = $solrBackend->buildStatement($mockStatementParts);
		$this->assertRegExp('/(?:^|&)q=foo:bar(?:$|&)/', $statement);
	}
	
	/**
	 * @test
	 */
	public function buildStatementAddsLimitIfNoLimitWasGiven() {
		$mockStatementParts = array(
			'fields' => array(),
			'where' => array(),
			'facet' => array(),
			'additionalWhereClause' => array(),
			'orderings' => array(),
			'limit' => array(),
		);
		
		$solrBackend = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackend', array('dummy'));
		
		$statement = $solrBackend->buildStatement($mockStatementParts);
		$this->assertRegExp('/(?:^|&)rows=\d{5,8}(?:$|&)/', $statement);
	}

}
?>