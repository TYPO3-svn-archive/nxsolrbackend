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
 * Filters node-tuples based on the outcome of a range operation.
 *
 *
 * @package Nxsolrbackend
 * @subpackage Persistence\QOM
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @access private
 */
class Tx_Nxsolrbackend_Persistence_QOM_Range extends Tx_Extbase_Persistence_QOM_Constraint implements Tx_Nxsolrbackend_Persistence_QOM_RangeInterface {
	
	/**
	 * @var Tx_Extbase_Persistence_QOM_DynamicOperandInterface
	 */
	protected $operand;
	
	/**
	 * @var Tx_Extbase_Persistence_QOM_StaticOperandInterface
	 */
	protected $lowerBoundOperand;
	
	/**
	 * @var Tx_Extbase_Persistence_QOM_StaticOperandInterface
	 */
	protected $upperBoundOperand;
	
	/**
	 *
	 * @param Tx_Extbase_Persistence_QOM_DynamicOperandInterface $operand
	 * @param Tx_Extbase_Persistence_QOM_StaticOperandInterface $lowerBoundOperand
	 * @param Tx_Extbase_Persistence_QOM_StaticOperandInterface $upperBoundOperand
	 */
	public function __construct(Tx_Extbase_Persistence_QOM_DynamicOperandInterface $operand, $lowerBoundOperand, $upperBoundOperand) {
		$this->operand = $operand;
		$this->lowerBoundOperand = $lowerBoundOperand;
		$this->upperBoundOperand = $upperBoundOperand;
	}

	/**
	 *
	 * Gets the first operand.
	 *
	 * @return Tx_Extbase_Persistence_QOM_DynamicOperandInterface the operand; non-null
	 */
	public function getOperand() {
		return $this->operand;
	}

	/**
	 * Gets the lower bound operand
	 *
	 * @return Tx_Extbase_Persistence_QOM_StaticOperandInterface the operand; non-null
	 */
	public function getLowerBoundOperand() {
		return $this->lowerBoundOperand;
	}
	
	/**
	 * Gets the upper bound operand
	 *
	 * @return Tx_Extbase_Persistence_QOM_StaticOperandInterface the operand; non-null
	 */
	public function getUpperBoundOperand() {
		return $this->upperBoundOperand;
	}
	
}

?>