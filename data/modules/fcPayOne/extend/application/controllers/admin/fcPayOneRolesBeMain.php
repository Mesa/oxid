<?php

/** 
 * PAYONE OXID Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE OXID Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE OXID Connector.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.payone.de
 * @copyright (C) Payone GmbH
 * @version   OXID eShop CE
 */
 
class fcPayOneRolesBeMain extends fcPayOneRolesBeMain_parent {
    
    /**
     * Add the PAYONE main node to the navigation
     *
     * @return string
	 * @extend render
     */
	public function render() {
		$sReturn = parent::render();
		
        $aDynRights 					= $this->_aViewData['aDynRights'];
        $oRights = $this->getRights();
		$aDynRights['fcpo_admin_title'] = $oRights->getViewRightsIndex( 'fcpo_admin_title' );
        $this->_aViewData['aDynRights'] = $aDynRights;
        
        return $sReturn;
	}
   
}