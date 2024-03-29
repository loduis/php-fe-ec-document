<?php

declare(strict_types=1);

namespace FEEC;

use XML\Support\Element;
use XML\Support\Single;

class Invoice extends Document\Contract
{
    public function __construct(array $data = [])
    {
        parent::init($data, [
            'reason' => 'string',
            'reference' => 'string',
            'discount' => 'float',
            'tip' => 'float',
            'payments' => 'array',
            'withholding' => Element::class,
            'withholdings' => 'array',
        ]);
    }

    public function toArray(): array
    {
        return [
            'infoTributaria' => $this->taxInfo,
            'infoFactura' => [
                'fechaEmision' => $this->date,
                'dirEstablecimiento' => $this->supplier->address->location,
                'contribuyenteEspecial' => $this->supplier->specialTaxpayer,
                'obligadoContabilidad' => $this->requiredAccounting,
                'tipoIdentificacionComprador' => $this->customer->identification->type,
                'guiaRemision' => $this->reference,
                'razonSocialComprador' => $this->customer->name,
                'identificacionComprador' => $this->customer->identification->number,
                'direccionComprador' => $this->customer->address->main,
                'totalSinImpuestos' => $this->net,
                'totalDescuento' => $this->discount,
                'totalConImpuestos' => $this->taxes,
                'propina' => $this->tip ?? '0.00',
                'importeTotal' => $this->total, // preguntar
                'moneda' => $this->currency, // hay que preguntar si es un currency code
                'pagos' => $this->payments,
                'valorRetIva' => $this->withholding->vat,
                'valorRetRenta' => $this->withholding->income,
            ],
            'detalles' => $this->items,
            'retenciones' => $this->withholdings,
            'infoAdicional' => $this->extraInfo,
        ];
    }

    protected function getExtraInfo(): array
    {
        $entries = [
            // 'Dirección' => $this->customer->address->main,
            'Teléfono' => $this->customer->phone,
            'Email' => $this->customer->email,
            'Observaciones' => $this->comments,
        ];
        $result = [];
        foreach ($entries as $field => $value) {
            if ($value) {
                $result[] = new Single($value, ['nombre' => $field]);
            }
        }

        return ['campoAdicional' => $result];
    }

    protected function getItems(iterable $items): array
    {
        return $this->mapItems('codigoPrincipal', $items);
    }

    protected function mapTaxes(iterable $taxes): array
    {
        return $this->map($taxes, function ($tax) {
            return [
                'codigo' => $tax->code,
                'codigoPorcentaje' => $tax->rate_code,
                'descuentoAdicional' => $tax->discount,
                'baseImponible' => $tax->base,
                'tarifa' => $tax->rate,
                'valor' => $tax->amount,
                'valorDevolucionIva' => $tax->return,
            ];
        });
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

    protected function getPayments(?iterable $payments): array
    {
        return [
            'pago' => $this->map($payments ?? [], function (iterable $payment) {
                return [
                    'formaPago' => $payment->method,
                    'total' => $payment->amount,
                    'plazo' => $payment->due_days,
                    'unidadTiempo' => $payment->due_days !== null ? 'dias' : null,
                ];
            })
        ];
    }

    protected function  getWithholdings(?iterable $taxes)
    {
        return [
            'retencion' => $this->map($taxes ?? [], function ($tax) {
                return [
                    'codigo' => $tax->code,
                    'codigoPorcentaje' => $tax->rate_code,
                    'tarifa' => $tax->rate,
                    'valor' => $tax->amount,
                ];
            })
        ];
    }

    protected function getName(): string
    {
        return 'factura';
    }

    protected function getVersion(): string
    {
        return '2.1.0';
    }

    protected function getType(): string
    {
        return DOC_INVOICE;
    }
}