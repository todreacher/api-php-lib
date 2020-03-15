<?php
// Copyright 1999-2020. Plesk International GmbH.

namespace PleskX\Api\Struct\Site;

class ResultChangeProperty extends \PleskX\Api\Struct
{
    /** @var array */
    public $results = [];

    public function __construct($apiResponse)
    {
        foreach ($apiResponse->site->set as $result) {
            $this->results[(string)$result->name] = (string)$result->value;
        }
    }
}
