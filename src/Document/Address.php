<?php

declare(strict_types=1);

namespace FEEC\Document;

use XML\Support\Element;

class Address extends Element
{
    protected $fillable = [
        'main' => 'string',
        'location' => 'string',
    ];

    protected function getLocation($value)
    {
        return $value ?? $this->main;
    }
}
