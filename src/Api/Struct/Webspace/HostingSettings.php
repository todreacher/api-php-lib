<?php
// Copyright 1999-2020. Plesk International GmbH.

namespace PleskX\Api\Struct\Webspace;

class HostingSettings extends \PleskX\Api\Struct
{
    /** @var array */
    public $properties;

    public function __construct($apiResponse)
    {
        $this->properties = [];

        foreach ($apiResponse->webspace->get->result->data->{'hosting'}->vrt_hst->property as $value) {
            $this->properties[(string)$value->name] = (string)$value->value;
        }
    }
}
