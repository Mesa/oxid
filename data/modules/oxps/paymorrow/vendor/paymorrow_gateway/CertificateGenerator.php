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

/* OXPS Patch - Start *///namespace Paymorrow;/* OXPS Patch - End */


class CertificateGenerator
{
    public function generateCertificate($certData)
    {
        $configParams = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privkey = openssl_pkey_new($configParams);

        //Now, using the private key, we can create the certificate. First we define the certificate parameters:

        //And then we can create the certificate:

        $csr = openssl_csr_new($certData, $privkey, $configParams);

        //Now we sign the certificate using the private key:

        $duration = 2 * 365;
        $sscert = openssl_csr_sign($csr, null, $privkey, $duration, $configParams);

        //Finally we can export the certificate and the private key:

        openssl_x509_export($sscert, $certout);
        $password = NULL;
        openssl_pkey_export($privkey, $pkout, $password, $configParams);
        //Note that a password is needed to export the private key. If a password is not needed, you must set $password
        //to NULL (don't set it to empty string as the private key password will be an empty string).

        return array(
            'privateKey' => $pkout,
            'certificate' => $certout
        );
    }
}

