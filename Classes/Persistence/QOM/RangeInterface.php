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
 * @version $Id$
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
interface Tx_Nxsolrbackend_Persistence_QOM_RangeInterface extends Tx_Extbase_Persistence_QOM_ConstraintInterface {

	/**
	 *
	 * Gets the first operand.
	 *
	 * @return Tx_Extbase_Persistence_QOM_DynamicOperandInterface the operand; non-null
	 */
	public function getOperand();

	/**
	 * Gets the lower bound operand
	 *
	 * @return Tx_Extbase_Persistence_QOM_StaticOperandInterface the operand; non-null
	 */
	public function getLowerBoundOperand();
	
	/**
	 * Gets the upper bound operand
	 *
	 * @return Tx_Extbase_Persistence_QOM_StaticOperandInterface the operand; non-null
	 */
	public function getUpperBoundOperand();
	
}

?>