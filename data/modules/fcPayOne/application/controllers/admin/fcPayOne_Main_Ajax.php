<?php
/**
 * Class manages payment countries
/**
 * Handles the complete communication with the PAYONE API
 */
 
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
 
class fcPayOne_Main_Ajax extends ajaxListComponent {
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;
    
    
    /**
     * Class constructor, sets all required parameters for requests.
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
    }    
    
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid',        'oxcountry', 0, 0, 1 )
                                        ),
                     'container2' => array(
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid', 'fcpopayment2country', 0, 0, 1 )
                                        )
                    );
    
    /**
     * Returns SQL query for data to fetch
     *
     * @return string
     */
    protected function _getQuery() {
        // looking for table/view
        $sCountryTable = getViewName('oxcountry');
        $sCountryId      = $this->_oFcpoHelper->fcpoGetRequestParameter( 'oxid' );
        $sSynchCountryId = $this->_oFcpoHelper->fcpoGetRequestParameter( 'synchoxid' );        
        $sType           = $this->_oFcpoHelper->fcpoGetRequestParameter( 'type' );

        // category selected or not ?
        if ( !$sCountryId) {
            // which fields to load ?
            $sQAdd = " from $sCountryTable where $sCountryTable.oxactive = '1' ";
        } else {

            $sQAdd  = " from fcpopayment2country left join $sCountryTable on $sCountryTable.oxid=fcpopayment2country.fcpo_countryid ";
            $sQAdd .= "where $sCountryTable.oxactive = '1' and fcpopayment2country.fcpo_paymentid = '$sCountryId' and fcpopayment2country.fcpo_type = '{$sType}' ";
        }

        if ( $sSynchCountryId && $sSynchCountryId != $sCountryId ) {
            $sQAdd .= "and $sCountryTable.oxid not in ( ";
            $sQAdd .= "select $sCountryTable.oxid from fcpopayment2country left join $sCountryTable on $sCountryTable.oxid=fcpopayment2country.fcpo_countryid ";
            $sQAdd .= "where fcpopayment2country.fcpo_paymentid = '$sSynchCountryId' and fcpopayment2country.fcpo_type = '{$sType}' ) ";
        }

        return $sQAdd;
    }

    /**
     * Adds chosen country to payment
     *
     * @return null
     */
    public function addpaycountry() {
        $aChosenCntr = $this->_getActionIds( 'oxcountry.oxid' );
        $soxId       = $this->_oFcpoHelper->fcpoGetRequestParameter( 'synchoxid');
        $sType       = $this->_oFcpoHelper->fcpoGetRequestParameter( 'type' );
        if ( $this->_oFcpoHelper->fcpoGetRequestParameter( 'all' ) ) {
            $sCountryTable = getViewName('oxcountry');
            $aChosenCntr = $this->_getAll( $this->_addFilter( "select $sCountryTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew( 'oxbase' );
                $oObject2Payment->init( 'fcpopayment2country' );
                $oObject2Payment->fcpopayment2country__fcpo_paymentid  = new oxField($soxId);
                $oObject2Payment->fcpopayment2country__fcpo_countryid  = new oxField($sChosenCntr);
                $oObject2Payment->fcpopayment2country__fcpo_type       = new oxField($sType);
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen country from payment
     *
     * @return null
     */
    public function removepaycountry() {
        $oDbInstance    = oxDb::getInstance();
        $oDb            = oxDb::getDb();
        $aChosenCntr = $this->_getActionIds( 'fcpopayment2country.oxid' );
        if ( $this->_oFcpoHelper->fcpoGetRequestParameter( 'all' ) ) {
            $sQ = $this->_addFilter( "delete fcpopayment2country.* ".$this->_getQuery() );
            $oDb->Execute( $sQ );
        } elseif ( is_array( $aChosenCntr ) ) {
            $sQ = "delete from fcpopayment2country where fcpopayment2country.oxid in (" . implode( ", ", $oDbInstance->quoteArray( $aChosenCntr ) ) . ") ";
            $oDb->Execute( $sQ );
        }
    }
    
}
