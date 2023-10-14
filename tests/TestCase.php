<?php

namespace FEEC\Tests;

abstract class TestCase extends \XML\Tests\TestCase
{
    protected function assertSchema($doc)
    {
        libxml_use_internal_errors(true);
        $res = $doc->validate(__DIR__ . '/XML-XSD/' . $doc->name . '_V' . $doc->version . '.xsd');
        if (!$res) {
            $errors = libxml_get_errors();
            print_r($errors);
        }
        $this->assertTrue($res);
    }
}