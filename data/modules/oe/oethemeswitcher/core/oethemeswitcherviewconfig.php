<?php
/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales Theme Switcher is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Theme Switcher is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Theme Switcher.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * View config data access class. Keeps most
 * of getters needed for formatting various urls,
 * config parameters, session information etc.
 */
class oeThemeSwitcherViewConfig extends oeThemeSwitcherViewConfig_parent
{
    /**
     * User Agent.
     *
     * @var object
     */
    protected $_oUserAgent = null;

    /**
     * User Agent getter.
     *
     * @return oeThemeSwitcherUserAgent
     */
    public function oeThemeSwitcherGetUserAgent()
    {
        if (is_null($this->_oUserAgent)) {
            $this->_oUserAgent = oxNew('oeThemeSwitcherUserAgent');
        }

        return $this->_oUserAgent;
    }
}
