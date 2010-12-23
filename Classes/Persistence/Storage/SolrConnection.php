<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Lienhart Woitok <lienhart.woitok@netlogix.de>
 *  All rights reserved
 *
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
 * A communication handler for solr server
 *
 * @package Nxsolrbackend
 * @subpackage Persistence\Storage
  */
class Tx_Nxsolrbackend_Persistence_Storage_SolrConnection implements t3lib_Singleton {

	/**
	 * The URI to the Solr server, eg. http://solr.example.org:8080/solr/
	 * @var string
	 */
	protected $solrServer;

	/**
	 * URL to use for queries. This will be appended to the solrServer property.
	 * @var string
	 */
	protected $queryUrl = 'select?version=2.2&wt=phps';
	
	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	
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
		if (isset($frameworkConfiguration['persistence']['tx_nxsolrbackend'])) {
			$settings = $frameworkConfiguration['persistence']['tx_nxsolrbackend'];
		}
		
		if (isset($settings['server']) && $settings['server'] !== '') {
			$this->solrServer = $settings['server'];
		} else {
			$globalConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['nxsolrbackend']);
			$this->solrServer = $globalConfiguration['solrurl'];
		}
	}
	
	/**
	 * Calls solr
	 *
	 * @param $query
	 * @return array value
	 */
	public function query($statement) {
		$url = $this->solrServer . $this->queryUrl . $statement;

		$errors = array();

		//echo $url;
		$serializedResult = t3lib_div::getUrl($url, 0, FALSE, $errors);
		// TODO check for errors
		
		$result = unserialize($serializedResult);
		return $result;
	}

		
}
?>