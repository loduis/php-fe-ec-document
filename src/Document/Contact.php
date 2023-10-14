<?php

declare(strict_types=1);

namespace FEEC\Document;

use XML\Support\Element;

class Contact extends Element
{
    protected $fillable = [
        'name' => 'string',
        'tradename' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'withholdingAgent' => 'int',
        'regimeMicroenterprise' => 'bool',
        'specialTaxpayer' => 'int',
        'requiredAccounting' => 'bool',
        'address' => Address::class,
        'identification' => Element::class,
    ];
}
