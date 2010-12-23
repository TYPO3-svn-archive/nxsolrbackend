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
 * The QueryFactory used to create queries against the storage backend
 *
 * @package Nxsolrbackend
 * @subpackage Persistence
 * @version $Id$
 */
class Tx_Nxsolrbackend_Persistence_QueryFactory extends Tx_Extbase_Persistence_QueryFactory { //implements Tx_Extbase_Persistence_QueryFactoryInterface, t3lib_Singleton {

	/**
	 * Creates a query object working on the given class name
	 *
	 * @param string $className The class name
	 * @return Tx_Extbase_Persistence_QueryInterface
	 */
	public function create($className) {
		$query = $this->objectManager->create('Tx_Nxsolrbackend_Persistence_Query', $className);
		$querySettings = $this->objectManager->create('Tx_Extbase_Persistence_Typo3QuerySettings');
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$querySettings->setStoragePageIds(t3lib_div::intExplode(',', $frameworkConfiguration['persistence']['storagePid']));
		$query->setQuerySettings($querySettings);

		return $query;
	}
}
?>