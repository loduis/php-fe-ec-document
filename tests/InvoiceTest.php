<?php

namespace FEEC\Tests;

use FEEC\Invoice;

use function FEEC\get_key;

use const FEEC\DOC_RUC;
use const FEEC\ENV_PROD;
use const FEEC\ENV_TEST;
use const FEEC\RIMPE_ENTREPRENEURS;
use const FEEC\RIMPE_POPULAR_BUSINESSES;
use const FEEC\TAX_VAT;
use const FEEC\VAT_RATE_12;

class InvoiceTest extends TestCase
{

    public function testExample1()
    {
        $doc = Invoice::fromArray([
            'environment' => ENV_TEST,
            'currency' => 'DOLAR',
            'date' => '23/03/2021',
            'prefix' => '001-002', // es la serie
            'number' => '000000020',
            'net' => '600.00',
            'discount' => '0.00',
            'total' => '600.00',
            'security_code' => '41530761',
            'supplier' => [
                'name' => 'Francisco Israel Teneda Gallardo',
                'tradename' => 'israteneda',
                'identification' => [
                    'number' => '0503501215001'
                ],
                'address' => [
                    'main' => 'Rio Tigre y Rio Ana Tenorio, Salcedo, Cotopaxi',
                ],
            ],
            'customer' => [
                'name' => 'ioet Inc.',
                'identification' => [
                    'type' => '08',
                    'number' => '47-10803393',
                ],
                'address' => [
                    'main' => '1491 Cypress Drive. Suite #853. Pebble Beach, California 93953'
                ],
                'email' => 'loduis@myabakus.com',
                'phone' => '31678969',
            ],
            'items' => [
                [
                    'code' => '831429900',
                    'description' => 'Otros Servicios de Diseño y Desarrollo de la Tecnología de la Información (IT) Para Redes y Sistemas, N.C.P. (831429900)',
                    'qty' => 1,
                    'price' => '600.00',
                    'net' => '600.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 0,
                            'base' => '600.00',
                            'rate' => 0,
                            'amount' => '0.00',
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 2, // IVA
                    'rate_code' => 0,
                    'discount' => '0.00',
                    'base' => '600.00',
                    'rate' => 0,
                    'amount' => '0.00',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '600.00',
                    'due_days' => 0,
                ],
            ],
            'comments' => 'Esto es una factura de prueba.'
        ]);

        $this->assertSchema($doc);
        $this->assertMatchesXmlSnapshot($doc->pretty());
    }

    public function testExample2()
    {

        $doc = Invoice::fromArray([
            'environment' => ENV_TEST,
            'currency' => 'DOLAR',
            'date' => '21/04/2021',
            'prefix' => '001-001', // es la serie
            'number' => '000003570',
            'net' => '53150.00',
            'discount' => '0.00',
            'total' => '59528.00',
            'security_code' => '00003570',
            'supplier' => [
                'name' => 'INMOBILIARIA CALDARIO SA',
                'tradename' => 'INMOBILIARIA CALDARIO SA',
                'identification' => [
                    'number' => '1790645231001'
                ],
                'address' => [
                    'main' => 'AMAZONAS Y PASAJE GUAYAS E3-131 EDF. RUMINAHUI PISO 8',
                ],
                'required_accounting' => true,
                'special_taxpayer' => '000'
            ],
            'customer' => [
                'name' => 'INMOBILIARIA MOTKE S.A.',
                'identification' => [
                    'type' => '04',
                    'number' => '0990995184001',
                ],
                'address' => [
                    'main' => 'AV. 9 DE OCTUBRE 729 Y  BOYACA'
                ],
                'email' => 'rolando.roc@gmail.com',
                'phone' => '042322000',
            ],
            'items' => [
                [
                    'code' => 'HONMOTKE',
                    'description' => 'HONORARIOS POR ADMINISTRACION, DIRECCION Y RESPONSABILIDAD TECNICA CUOTA 11 DEL 01 AL 28 MARZO DE 2021 PROYECTO RIOCENTRO QUITO',
                    'qty' => 1,
                    'price' => '53150.00',
                    'net' => '53150.00',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => 2, // IVA
                            'rate_code' => 2,
                            'base' => '53150.00',
                            'rate' => 12,
                            'amount' => '6378.00',
                        ],
                    ],
                ],
            ],
            'taxes' => [
                [
                    'code' => 2, // IVA
                    'rate_code' => 2,
                    'discount' => '0.00',
                    'base' => '53150.00',
                    'rate' => 12,
                    'amount' => '6378.00',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '59528.00',
                ],
            ],
            'comments' => 'HONORARIOS POR ADMINISTRACION, DIRECCION Y RESPONSABILIDAD TECNICA CUOTA 11 DEL 01 AL 28 DE MARZO DE 2021 PROYECTO RIOCENTRO QUITO DIRECCION: AV. 6 DE DICIEMBRE N21-245 TOMAS DE BERLANGA, CALLE PINZON'
        ]);
        $this->assertSchema($doc);
        $this->assertMatchesXmlSnapshot($doc->pretty());

    }

    public function testExample3()
    {

        $doc = Invoice::fromArray([
            'environment' => ENV_PROD,
            'currency' => 'DOLAR',
            'date' => '01/02/2024',
            'prefix' => '001-003', // es la serie
            'number' => '000000021',
            'net' => '392.86',
            'discount' => '0.00',
            'total' => '440.00',
            'security_code' => '12345678',
            'supplier' => [
                'name' => 'DAVID FERNANDO MARTINEZ PAEZ',
                'tradename' => 'P CLICK',
                'identification' => [
                    'number' => '1901941231001'
                ],
                'address' => [
                    'main' => 'Calle Base3 Sur y ROW',
                ],
                'required_accounting' =>false,
            ],
            'customer' => [
                'name' => 'ANA BELEN MOREJON ARCE',
                'identification' => [
                    'type' => DOC_RUC,
                    'number' => '1713337245001',
                ],
                'address' => [
                    'main' => 'SUCRE 06-61 Y LUCAS L MERA'
                ],
                'email' => [
                    'A@hotmail.com',
                    'R@hotmail.com'
                ],
                'phone' => '098 603 7038',
            ],
            'items' => [
                [
                    'code' => '005',
                    'description' => 'PLAN DIFUSIÓN DE NEGOCIO - Generación de Clientes por PPC',
                    'qty' => 1,
                    'price' => '392.8571',
                    'net' => '392.86',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => TAX_VAT, // IVA
                            'rate_code' => VAT_RATE_12,
                            'base' => '392.86',
                            'rate' => 12,
                            'amount' => '47.14',
                        ],
                    ],
                    'comments' => 'Administración de Anuncios de Pago en Facebook e Instagram.'
                ],
            ],
            'taxes' => [
                [
                    'code' => TAX_VAT, // IVA
                    'rate_code' => VAT_RATE_12,
                    // 'discount' => '0.00',
                    'base' => '392.86',
                    'rate' => 12,
                    'amount' => '47.14',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '440.00',
                ],
            ]
        ]);
        $this->assertSchema($doc);
        $this->assertMatchesXmlSnapshot($doc->pretty());

    }

    public function testExample4()
    {

        $doc = Invoice::fromArray([
            'environment' => ENV_TEST,
            'currency' => 'DOLAR',
            'date' => '01/02/2024',
            'prefix' => '001-003', // es la serie
            'number' => '000000021',
            'net' => '392.86',
            'discount' => '0.00',
            'total' => '440.00',
            'security_code' => '12345678',
            'supplier' => [
                'name' => 'DAVID FERNANDO MARTINEZ PAEZ',
                'tradename' => 'P CLICK',
                'identification' => [
                    'number' => '1901941231001'
                ],
                'address' => [
                    'main' => 'Calle Base3 Sur y ROW',
                ],
                'required_accounting' =>false,
                'rimpe_taxpayer' => RIMPE_ENTREPRENEURS
            ],
            'customer' => [
                'name' => 'ANA BELEN MOREJON ARCE',
                'identification' => [
                    'type' => DOC_RUC,
                    'number' => '1713337245001',
                ],
                'address' => [
                    'main' => 'SUCRE 06-61 Y LUCAS L MERA'
                ],
                'email' => [
                    'A@hotmail.com',
                    'R@hotmail.com'
                ],
                'phone' => '098 603 7038',
            ],
            'items' => [
                [
                    'code' => '005',
                    'description' => 'PLAN DIFUSIÓN DE NEGOCIO - Generación de Clientes por PPC',
                    'qty' => 1,
                    'price' => '392.8571',
                    'net' => '392.86',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => TAX_VAT, // IVA
                            'rate_code' => VAT_RATE_12,
                            'base' => '392.86',
                            'rate' => 12,
                            'amount' => '47.14',
                        ],
                    ],
                    'comments' => 'Administración de Anuncios de Pago en Facebook e Instagram.'
                ],
            ],
            'taxes' => [
                [
                    'code' => TAX_VAT, // IVA
                    'rate_code' => VAT_RATE_12,
                    // 'discount' => '0.00',
                    'base' => '392.86',
                    'rate' => 12,
                    'amount' => '47.14',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '440.00',
                ],
            ]
        ]);
        $this->assertSchema($doc);
        $this->assertMatchesXmlSnapshot($doc->pretty());
    }

    public function testExample5()
    {

        $doc = Invoice::fromArray([
            'environment' => ENV_TEST,
            'currency' => 'DOLAR',
            'date' => '01/02/2024',
            'prefix' => '001-003', // es la serie
            'number' => '000000021',
            'net' => '392.86',
            'discount' => '0.00',
            'total' => '440.00',
            'security_code' => '12345678',
            'supplier' => [
                'name' => 'DAVID FERNANDO MARTINEZ PAEZ',
                'tradename' => 'P CLICK',
                'identification' => [
                    'number' => '1901941231001'
                ],
                'address' => [
                    'main' => 'Calle Base3 Sur y ROW',
                ],
                'required_accounting' =>false,
                'rimpe_taxpayer' => RIMPE_POPULAR_BUSINESSES
            ],
            'customer' => [
                'name' => 'ANA BELEN MOREJON ARCE',
                'identification' => [
                    'type' => DOC_RUC,
                    'number' => '1713337245001',
                ],
                'address' => [
                    'main' => 'SUCRE 06-61 Y LUCAS L MERA'
                ],
                'email' => [
                    'A@hotmail.com',
                    'R@hotmail.com'
                ],
                'phone' => '098 603 7038',
            ],
            'items' => [
                [
                    'code' => '005',
                    'description' => 'PLAN DIFUSIÓN DE NEGOCIO - Generación de Clientes por PPC',
                    'qty' => 1,
                    'price' => '392.8571',
                    'net' => '392.86',
                    'discount' => '0.00',
                    'taxes' => [
                        [
                            'code' => TAX_VAT, // IVA
                            'rate_code' => VAT_RATE_12,
                            'base' => '392.86',
                            'rate' => 12,
                            'amount' => '47.14',
                        ],
                    ],
                    'comments' => 'Administración de Anuncios de Pago en Facebook e Instagram.'
                ],
            ],
            'taxes' => [
                [
                    'code' => TAX_VAT, // IVA
                    'rate_code' => VAT_RATE_12,
                    // 'discount' => '0.00',
                    'base' => '392.86',
                    'rate' => 12,
                    'amount' => '47.14',
                ],
            ],
            'payments' => [
                [
                    'method' => '20',
                    'amount' => '440.00',
                ],
            ]
        ]);
        $this->assertSchema($doc);
        $this->assertMatchesXmlSnapshot($doc->pretty());
    }

    public function testKey()
    {
        $key = get_key(
            '01022024', //date
            '01', // type doc
            '1901941231001', // nit
            '2', // env
            '001003', // local and point
            '000000021', // number
            '12345678', // code
            '1' // type issue
        );

        $this->assertEquals('0102202401190194123100120010030000000211234567818', $key);
    }
}
