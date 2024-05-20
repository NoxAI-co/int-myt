@php echo'<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; @endphp
<CreditNote
    xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2"
    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
    xmlns:sts="http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures"
    xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"
    xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2     http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-CreditNote-2.1.xsd">
    {{-- UBLExtensions
    @include('templates.xml._ubl_extensions') --}}
    <cbc:CustomizationID>05</cbc:CustomizationID>
    <cbc:ProfileExecutionID>2</cbc:ProfileExecutionID>
    <cbc:ID>{{ $NotaCredito->nro }}</cbc:ID>
    <cbc:UUID schemeID="2" schemeName="CUDE-SHA384">{{$CUDEvr}}</cbc:UUID>
    <cbc:IssueDate>{{ Carbon\Carbon::parse($NotaCredito->created_at)->format('Y-m-d') }}</cbc:IssueDate>
    <cbc:IssueTime>{{ Carbon\Carbon::parse($NotaCredito->created_at)->format('H:i:s') }}-05:00</cbc:IssueTime>
    <cbc:CreditNoteTypeCode></cbc:CreditNoteTypeCode>{{-- tipo de nota de credito --}}
    <cbc:DocumentCurrencyCode>COP</cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>{{count($items)}}</cbc:LineCountNumeric>
    
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>
        Sección de la factura la cual se le aplica la correción
        </cbc:ReferenceID>
        <cbc:ResponseCode>2</cbc:ResponseCode> {{-- codigo para correccion --}}
        <cbc:Description>Anulación de factura electrónica</cbc:Description>{{-- Concepto de Corrección para Notas crédito --}}
    </cac:DiscrepancyResponse>
    {{-- BillingReference --}}
    @include('templates.xml._billing_reference')
    {{-- AccountingSupplierParty --}}
    @include('templates.xml._accounting', ['node' => 'AccountingSupplierParty', 'data' =>  $data['Empresa'],'Empresa' => true])
    {{-- AccountingCustomerParty --}}
    @include('templates.xml._accounting', ['node' => 'AccountingCustomerParty', 'data' => $data['Cliente']])
    {{-- PaymentMeans --}}
    @include('templates.xml._payment_means')
    {{-- AllowanceCharges --}}
    {{--@include('xml._allowance_charges')--}}
    {{-- TaxTotals --}}

    {{-- Como se utiliza el mismo metodo para varias cosas, entonces le damos el nombre que recibe ese metodo, sabiendo que es una notacredito --}}
    @php $FacturaVenta = $NotaCredito; @endphp
    @include('templates.xml._tax_totals')
    {{-- LegalMonetaryTotal --}}
    @include('templates.xml._legal_monetary_total', ['node' => 'LegalMonetaryTotal'])
    {{-- CreditNoteLine --}}
    @include('templates.xml._credit_note_lines')
    <DATA>
        <UBL21>true</UBL21>
        <Partnership>
            <ID>99999999</ID>
            <TechKey>fc8eac422eba16e22ffd8c6f94b3f40a6e38162c</TechKey>
            <SetTestID>705cacf8-e4f1-4055-a5b2-095bc2fc7683</SetTestID>
        </Partnership>
    </DATA>
</CreditNote>
