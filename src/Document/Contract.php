<?php

// https://www.sri.gob.ec/facturacion-electronica#informaci%C3%B3n

declare(strict_types=1);

namespace FEEC\Document;

use XML\Support\Element;
use XML\Support\Single;
use XML\Document\Creator;
use FEEC\Document\Contact;
use DomainException;

use function FEEC\get_key;

use const FEEC\TYPE_EMISSION;
use const FEEC\DOC_RUC;
use const FEEC\DOC_ID_CARD;
use const FEEC\DOC_ID_FOREIGN;
use const FEEC\DOC_FINAL_CONSUMER;
use const FEEC\RIMPE_ENTREPRENEURS;
use const FEEC\RIMPE_POPULAR_BUSINESSES;

/**
 * @property taxes
 */
abstract class Contract extends \XML\Document
{
    protected $fillable = [
        'environment' => 'int',
        'date' => 'string',
        'prefix' => 'string',
        'number' => 'string',
        'net' => 'float',
        'total' => 'float',
        'key' => 'string',
        'securityCode' => 'int',
        'currency' => 'string',
        'customer' => Contact::class,
        'supplier' => Contact::class,
        'location' => Element::class,
        'items' => 'array',
        'taxes' => 'array',
        'comments' => 'string',
    ];

    protected function getTaxInfo()
    {
        return [
            'ambiente' => $this->environment,
            'tipoEmision' => $this->issue, // Normal es una constante por ahora
            'razonSocial' => $this->supplier->name,
            'nombreComercial' => $this->supplier->tradename,
            'ruc' => $this->supplier->identification->number,
            'claveAcceso' => $this->key,
            'codDoc' => $this->type,
            'estab' => $this->location->main,
            'ptoEmi' => $this->location->issue,
            'secuencial' => $this->number,
            'dirMatriz' => $this->supplier->address->main,
            'agenteRetencion' => $this->supplier->withholdingAgent,
            'contribuyenteRimpe' => $this->rimpeInfo
        ];
    }

    protected function getRimpeInfo(): ?string
    {
        $code = (int) $this->supplier->rimpeTaxpayer;

        if ($code === RIMPE_ENTREPRENEURS) {
            return 'CONTRIBUYENTE RÉGIMEN RIMPE';
        }
        if ($code === RIMPE_POPULAR_BUSINESSES) {
            return 'CONTRIBUYENTE NEGOCIO POPULAR - RÉGIMEN RIMPE';
        }

        return null;
    }

    protected function getIssue(): int
    {
        return TYPE_EMISSION;
    }

    protected function getTaxes(iterable $taxes)
    {
        return [
            'totalImpuesto' => $this->mapTaxes($taxes)
        ];
    }

    protected function mapItems(string $codeName, iterable $items): array
    {
        return [
            'detalle' => $this->map($items, function ($item) use ($codeName) {
                return [
                    $codeName => $item->code,
                    'descripcion' => $item->description,
                    'unidadMedida' => $item->unit ?? null,
                    'cantidad' => $item->qty,
                    'precioUnitario' => $item->price,
                    'descuento' => $item->discount,
                    'precioTotalSinImpuesto' => $item->net,
                    'detallesAdicionales' => [
                        'detAdicional' => $this->mapNotes($item->comments)
                    ],
                    'impuestos' => [
                        'impuesto' => $this->mapLineTaxes($item->taxes)
                    ],
                ];
            })
        ];
    }

    protected function mapNotes(?string $comments): ?array
    {
        if (!$comments) {
            return null;
        }

        $entries = [];

        foreach (explode("\n", $comments) as $i => $comment) {
            $entries[] = new Single('', [
                'nombre' => 'Detalle' . ($i + 1),
                'valor' => $comment
            ]);
        }

        return $entries;
    }

    protected function mapLineTaxes(iterable $taxes): array
    {
        return $this->map($taxes, function ($tax) {

            return [
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'tarifa' => $tax->rate,
                'baseImponible' => $tax->base,
                'valor' => $tax->amount,
            ];
        });
    }

    protected function getRequiredAccounting()
    {
        return  $this->supplier->requiredAccounting ? 'SI' : 'NO';
    }

    protected function map(iterable $entries, \Closure $callback)
    {
        $data = [];
        foreach ($entries as $entry) {
            $entry = Element::fromArray($entry);
            $data[] = $callback($entry);
        }

        return $data;
    }

    protected function creator(): Creator
    {
        return new Creator($this, [
            'id' => 'comprobante',
            'version' => $this->version
        ]);
    }

    protected function setPrefix(string $value): string
    {
        if (strpos($value, '-') === false) {
            throw new \InvalidArgumentException('No separator - is present in prefix: ' . $value);
        }
        [$main, $issue] = explode('-', $value);
        $this->location = [
            'main' => $main,
            'issue' => $issue,
        ];

        return $main . $issue;
    }

    protected function getKey(?string $value): string
    {
        $value = $value ?: get_key(
            str_replace('/', '', $this->date), //date
            $this->type, // type doc
            $this->supplier->identification->number, // nit
            $this->environment, // env
            $this->location->main . $this->location->issue, // local and point
            $this->number, // number
            $this->securityCode, // code
            $this->issue // type issue
        );

        if (strlen($value) !== 49) {
            throw new DomainException('Invalid access key: ' . $value);
        }

        return $value;
    }

    protected function setCustomer($customer)
    {
        $id = $customer['identification'] ?? null;
        if (isset($id['type']) && !in_array($id['type'], [
            DOC_RUC,
            DOC_ID_CARD,
            DOC_ID_FOREIGN,
            DOC_FINAL_CONSUMER
        ])) {
            throw new DomainException('Invalid document type: ' . $id['type']);
        }
        return $customer;
    }

    abstract protected function mapTaxes(iterable $taxes): array;
}
