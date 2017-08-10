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
 
class fcporequestlog extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'fcporequestlog';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'fcporequestlog';
    

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'fcporequestlog' );
    }

    
    /**
     * Get request as array
     * 
     * @return array
     */
    public function getRequestArray() {
        return $this->getArray($this->fcporequestlog__fcpo_request->rawValue);
    }
    

    /**
     * Get response as array
     * 
     * @return array
     */
    public function getResponseArray() {
        return $this->getArray($this->fcporequestlog__fcpo_response->rawValue);
    }
    

    /**
     * Get a array from a serialized array or false if not unserializable
     * 
     * @return array
     */
    protected function getArray($sParam) {
        $aArray     = unserialize($sParam);
        $mReturn    = (is_array($aArray)) ? $aArray : false;
        
        return $mReturn;
    }
}