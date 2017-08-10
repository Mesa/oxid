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

class RequestController
{
    private $gateway;
    private $resourceProxy;

    public function pmVerify($data)
    {
        $_SESSION["pmVerify"] = $data;

        return $this->gateway->prepareOrder($data);
    }

	public function pmConfirm()
	{
		return $this->gateway->confirmOrder();
	}
	
    public function getResource($path, $session_id = null)
    {
        return $this->resourceProxy->getResource($path, $session_id);
    }

    /**
     * @param mixed $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param mixed $resourceProxy
     */
    public function setResourceProxy($resourceProxy)
    {
        $this->resourceProxy = $resourceProxy;
    }

}

