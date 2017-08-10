<?php
/**
 * This file is part of OXID eSales Paymorrow module.
 *
 * OXID eSales Paymorrow module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class OxpsPaymorrowResourceCache.
 */
class OxpsPaymorrowResourceCache extends OxpsPaymorrowModule
{

    /**
     * @var string Folder name in temp files directory to keep resources cache in.
     */
    protected $_sResourceCacheFolder = 'media';


    /**
     * From Paymorrow documentation:
     *
     * 6.4.1 Caching
     * It’s required to cache all resources (css and JS) in eshop system to avoid too many calls to Paymorrow resource service.
     * Cache is valid for 4hours.
     * For further performance improvement it’s required to load pmfunc.js before coming on payment method selection page.
     *
     * @var int Cache time to live in seconds.
     */
    protected $_iResourceCacheTtl = 14400;


    /**
     * Set resources cache folder name inside temp files directory.
     *
     * @param string $sFolderName
     */
    public function setCacheFolder( $sFolderName )
    {
        $this->_sResourceCacheFolder = (string) $sFolderName;
    }

    /**
     * Get resources cache folder name inside temp files directory.
     *
     * @return string
     */
    public function getCacheFolder()
    {
        return $this->_sResourceCacheFolder;
    }

    /**
     * Set resource cache time to live in seconds.
     *
     * @param int $iSeconds
     */
    public function setCacheTtl( $iSeconds )
    {
        $this->_iResourceCacheTtl = (int) $iSeconds;
    }

    /**
     * Get resource cache time to live in seconds.
     *
     * @return int
     */
    public function getCacheTtl()
    {
        return $this->_iResourceCacheTtl;
    }


    /**
     * Save file to cache.
     *
     * @param string $sFileName
     * @param mixed  $mFileContent
     *
     * @return bool|int
     */
    public function push( $sFileName, $mFileContent )
    {
        $sPath = $this->_getCacheFilePath( $sFileName );

        if ( is_null( $sPath ) ) {
            return false;
        }

        return file_put_contents( $sPath, serialize( $mFileContent ) );
    }

    /**
     * Get file from cache.
     *
     * @param string $sFileName
     *
     * @return bool|mixed
     */
    public function pop( $sFileName )
    {
        $sPath = $this->_getCacheFilePath( $sFileName );

        if ( is_null( $sPath ) ) {
            return false;
        }

        if ( $this->_expired( $sPath ) ) {
            @unlink( $sPath );

            return false;
        }

        return unserialize( file_get_contents( $sPath ) );
    }

    /**
     * Clean all resource cache.
     */
    public function cleanCache()
    {
        self::cleanTmp( $this->_getCacheFilePath() );
    }


    /**
     * Get full path to a file inside resource cache folder.
     *
     * @param string $sFileName If empty, full path to resource cache folder is returned
     *
     * @return null|string Null if resource cache folder is not available, full path string on success.
     */
    protected function _getCacheFilePath( $sFileName = '' )
    {
        $sTmpDirFullPath          = realpath( oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) );
        $sResourceCacheFolderName = $this->getCacheFolder();
        $sResourceCachePath       = $sTmpDirFullPath . DIRECTORY_SEPARATOR . $sResourceCacheFolderName;

        // Try to create resource cache folder if it does not exist
        if ( !is_dir( $sResourceCachePath ) ) {
            @mkdir( $sResourceCachePath, 0777 );
        }

        if ( !is_dir( $sResourceCachePath ) ) {
            return null;
        }

        return empty( $sFileName )
            ? $sResourceCachePath
            : $sResourceCachePath . DIRECTORY_SEPARATOR . basename( $sFileName );
    }

    /**
     * Check if file exists and was created not too long ago.
     *
     * @param string $sFilePath
     *
     * @return bool False if file is missing or is too old/expired, True otherwise.
     */
    protected function _expired( $sFilePath )
    {
        return (
            !is_file( $sFilePath ) or
            ( filemtime( $sFilePath ) <= strtotime( sprintf( 'now - %d second', $this->getCacheTtl() ) ) )
        );
    }
}
