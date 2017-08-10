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
 
 /*
  * Servers which are allowed to send transactionstatus requests are configured here.
  * Just add or remove IP-adresses from this list as needed.
  * The * can be used as wildcard-character to allow every number between 1 and 255 in the given section of the IP address
  * Please don't touch this if you don't know what you are doing - the pre-configured IPs are the IPs from PAYONEs responsible servers
  */
 
$aWhitelist = array(
    '185.60.20.*',
    '213.178.72.196',
    '213.178.72.197',
    '217.70.200.*',
);