<?php
// Copyright 1999-2020. Plesk International GmbH.

namespace PleskX\Api\Operator;

use PleskX\Api\Struct\Site as Struct;

class Site extends \PleskX\Api\Operator
{
    const PROPERTIES_HOSTING = 'hosting';

    /**
     * @param array $properties
     *
     * @return Struct\Info
     */
    public function create(array $properties)
    {
        $packet = $this->_client->getPacket();
        $info = $packet->addChild($this->_wrapperTag)->addChild('add');

        $infoGeneral = $info->addChild('gen_setup');
        foreach ($properties as $name => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $infoGeneral->addChild($name, $value);
        }

        // set hosting properties
        if (isset($properties[static::PROPERTIES_HOSTING]) && is_array($properties[static::PROPERTIES_HOSTING])) {
            $hostingNode = $info->addChild('hosting')->addChild('vrt_hst');
            foreach ($properties[static::PROPERTIES_HOSTING] as $name => $value) {
                $propertyNode = $hostingNode->addChild('property');
                $propertyNode->addChild('name', $name);
                $propertyNode->addChild('value', $value);
            }
        }

        $response = $this->_client->request($packet);

        return new Struct\Info($response);
    }

    /**
     * @param string $field
     * @param int|string $value
     *
     * @return bool
     */
    public function delete($field, $value)
    {
        return $this->_delete($field, $value);
    }

    /**
     * @param string $field
     * @param int|string $value
     *
     * @return Struct\GeneralInfo
     */
    public function get($field, $value)
    {
        $items = $this->_getItems(Struct\GeneralInfo::class, 'gen_info', $field, $value);

        return reset($items);
    }

    /**
     * @param string $field
     * @param int|string $value
     *
     * @return Struct\HostingInfo|null
     */
    public function getHosting($field, $value)
    {
        $items = $this->_getItems(Struct\HostingInfo::class, 'hosting', $field, $value, function ($node) {
            return isset($node->vrt_hst);
        });

        return empty($items) ? null : reset($items);
    }

    /**
     * @return Struct\GeneralInfo[]
     */
    public function getAll()
    {
        return $this->_getItems(Struct\GeneralInfo::class, 'gen_info');
    }

    /**
     * @param string $name
     * @return Struct\GeneralInfo
     */
    public function getByName(string $name)
    {
        return $this->get("name", $name);
    }

    /**
     * @param string $siteName
     * @param string $propertyName
     * @param string $propertyValue
     * @return Struct\ResultChangeProperty
     */
    public function setPropertyValueBySiteName(string $siteName, string $propertyName, string $propertyValue)
    {
        $packet = $this->_client->getPacket();
        $getTag = $packet->addChild($this->_wrapperTag)->addChild('set');

        $getTag->addChild('filter')->addChild('name', $siteName);
        $property = $getTag->addChild('values')->addChild('hosting')->addChild('vrt_hst')->addChild('property');
        $property->addChild('name', $propertyName);
        $property->addChild('value', $propertyValue);

        $response = $this->_client->request($packet, \PleskX\Api\Client::RESPONSE_FULL);
        return new Struct\ResultChangeProperty($response);
    }
}
