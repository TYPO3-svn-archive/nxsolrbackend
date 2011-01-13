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

class Tx_Nxsolrbackend_Persistence_QueryResultTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	
	/**
	 *
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $mockObjectManager;
	
	/**
	 *
	 * @var Tx_Extbase_Persistence_Mapper_DataMapper
	 */
	protected $mockDataMapper;
	
	/**
	 * @var Tx_Extbase_Persistence_Storage_SolrBackendInterface
	 */
	protected $mockStorageBackend;
	
	/**
	 *
	 * @var Tx_Nxsolrbackend_Persistence_FacetResultInterface
	 */
	protected $mockFacetResult;
	
	/**
	 *
	 * @var Tx_Nxsolrbackend_Persistence_QueryInterface
	 */
	protected $mockQuery;
	
	/**
	 *
	 * @var Tx_Extbase_Persistence_QuerySettingsInterface
	 */
	protected $mockQuerySettings;
	
	/**
	 *
	 * @var array
	 */
	protected $mockQueryResponse;
	
	public function setUp() {
		$this->mockDataMapper = $this->getMock('Tx_Extbase_Persistence_Mapper_DataMapper');
		$this->mockStorageBackend = $this->getMock('Tx_Nxsolrbackend_Persistence_Storage_SolrBackendInterface');
		$this->mockFacetResult = $this->getMock('Tx_Nxsolrbackend_Persistence_FacetResultInterface', array('getResult', '__construct'), array(), '', FALSE);
		$this->mockObjectManager = $this->getMock('Tx_Extbase_Object_ObjectManager', array('create', 'get'));
		$this->mockObjectManager->expects($this->any())->method('create')->with('Tx_Nxsolrbackend_Persistence_FacetResultInterface')->will($this->returnValue($this->mockFacetResult));
		$this->mockQuerySettings = $this->getMock('Tx_Extbase_Persistence_QuerySettingsInterface');
		$this->mockQuery = $this->getMock('Tx_Nxsolrbackend_Persistence_QueryInterface', array(), array(), '', FALSE);
		$this->mockQuery->expects($this->any())->method('getQuerySettings')->will($this->returnValue($this->mockQuerySettings));
		$this->mockQueryResponse = array(
			'response' => array(
				'docs' => array(),
				'numFound' => 0,
			),
		);
	}
	
	public function tearDown() {
		$this->mockObjectManager = NULL;
		$this->mockDataMapper = NULL;
		$this->mockStorageBackend = NULL;
		$this->mockFacetResult = NULL;
		$this->mockQuerySettings = NULL;
		$this->mockQuery = NULL;
		$this->mockQueryResponse = NULL;
	}
	
	/**
	 * @test
	 */
	public function initializeRetrievesData() {
		$queryResult = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_QueryResult', array('dummy'), array($this->mockQuery));
		$queryResult->injectObjectManager($this->mockObjectManager);
		
		$this->mockStorageBackend->expects($this->once())->method('getObjectAndFacetDataByQuery')->with($this->mockQuery)->will($this->returnValue($this->mockQueryResponse));
		$queryResult->injectStorageBackend($this->mockStorageBackend);
		
		$queryResult->injectDataMapper($this->mockDataMapper);
		
		$queryResult->_call('initialize');
	}
	
	/**
	 * @test
	 */
	public function initializeMapsData() {
		$queryResult = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_QueryResult', array('dummy'), array($this->mockQuery));
		$queryResult->injectObjectManager($this->mockObjectManager);
		
		$mockDocs = array(array('id' => 1));
		$this->mockQueryResponse['response']['docs'] = $mockDocs;
		$this->mockQueryResponse['response']['numFound'] = count($mockDocs);
		
		$this->mockStorageBackend->expects($this->any())->method('getObjectAndFacetDataByQuery')->with($this->mockQuery)->will($this->returnValue($this->mockQueryResponse));
		$queryResult->injectStorageBackend($this->mockStorageBackend);
		
		$this->mockQuery->expects($this->atLeastOnce())->method('getType')->will($this->returnValue('test_table'));
		
		$this->mockDataMapper->expects($this->once())->method('map')->with('test_table', $mockDocs)->will($this->returnValue(array()));
		$queryResult->injectDataMapper($this->mockDataMapper);
		
		$queryResult->_call('initialize');
	}
	
	/**
	 * @test
	 */
	public function initializeDoesNotMapDataIfRawQueryResultWasRequested() {
		$queryResult = $this->getAccessibleMock('Tx_Nxsolrbackend_Persistence_QueryResult', array('dummy'), array($this->mockQuery));
		$queryResult->injectObjectManager($this->mockObjectManager);
		
		$this->mockStorageBackend->expects($this->any())->method('getObjectAndFacetDataByQuery')->with($this->mockQuery)->will($this->returnValue($this->mockQueryResponse));
		$queryResult->injectStorageBackend($this->mockStorageBackend);
		
		$this->mockDataMapper->expects($this->never())->method('map');
		$queryResult->injectDataMapper($this->mockDataMapper);
		
		$this->mockQuerySettings->expects($this->atLeastOnce())->method('getReturnRawQueryResult')->will($this->returnValue(TRUE));
		
		$queryResult->_call('initialize');
	}
}

?>