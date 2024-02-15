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
        'specialTaxpayer' => 'int',
        'rimpeTaxpayer' => 'int',
        'withholdingAgent' => 'int',
        'requiredAccounting' => 'bool',
        'address' => Address::class,
        'identification' => Element::class,
    ];

    protected function setEmail($email)
    {
        if (is_array($email)) {
            $email = implode(', ', $email);
        }

        return $email;
    }
}
