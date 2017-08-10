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
 * Main shop configuration class.
 *
 * @package core
 */
class oeThemeSwitcherConfig extends oeThemeSwitcherConfig_parent
{
    /**
     * Bool variable true if modules configs are loaded, otherwise false
     *
     * @var bool
     */
    protected $_blIsModuleConfigLoaded = false;

    /**
     * Theme manager object
     *
     * @var oeThemeSwitcherThemeManager
     */
    protected $_oThemeManager = null;

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $sName config parameter name
     *
     * @return mixed
     */
    public function getConfigParam($sName)
    {
        $sReturn = parent::getConfigParam($sName);

        if ($sName == "sCustomTheme") {
            //load module configs
            if (!$this->_blIsModuleConfigLoaded) {
                $this->_loadVarsFromDb($this->getShopId(), null, oxConfig::OXMODULE_MODULE_PREFIX);
                $this->_blIsModuleConfigLoaded = true;
            }

            // check for mobile devices
            if ($this->oeThemeSwitcherGetThemeManager()->isMobileThemeRequested() && !$this->isAdmin()) {
                return $this->_aConfigParams['sOEThemeSwitcherMobileTheme'];
            }
        }

        return $sReturn;
    }

    /**
     * Return current active theme
     *
     * @return string
     */
    public function oeThemeSwitcherGetActiveThemeId()
    {
        $sCustomTheme = $this->getConfigParam('sCustomTheme');
        if ($sCustomTheme) {
            return $sCustomTheme;
        }

        return $this->getConfigParam('sTheme');
    }

    /**
     * Return theme manager
     *
     * @return oeThemeSwitcherThemeManager
     */
    public function oeThemeSwitcherGetThemeManager()
    {
        if ($this->_oThemeManager == null) {
            $this->_oThemeManager = oxNew('oeThemeSwitcherThemeManager');
        }

        return $this->_oThemeManager;
    }
}
