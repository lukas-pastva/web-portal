<?php

function sysDesignViewEn($print = false)
{

    $section = sysSectionGet($_REQUEST['origin']);
    $id = escapeQuotes($_REQUEST['id']);
    $module = sysModuleGet($section['id']);
    $attrs = sysModuleAttributesGet($section['id']);

    $template = $module['template'];


    //now mix the template with values in curly brackets
    $dataOutput = $module['template'];

    //printing out
    if ($print) {
        sysPrintHtmlHeader(true);
        echo '<body style="height:100%"><div class="wrapper" style="height:100%">';
    } else {
        sysPrintHeader();
        sysPrintBlockHeader(10, '');
    }

    //echo $dataOutput;
    //eval($dataOutput);

    $data = sysModuleGetRows($section['type'] == 'abstract', $section['id'], $id);
    $data = array_pop($data);

    $businessCompany = sysModuleGetRows(true, 'businessCompany', $data['businessCompanyId']);
    $businessCompany = array_pop($businessCompany);
    $businessCompanyAttrs = sysModuleAttributesGet('businessCompany');

    $businessCustomer = sysModuleGetRows(true, 'businessCustomer', $data['businessCustomerId']);
    $businessCustomer = array_pop($businessCustomer);
    $businessCustomerAttrs = sysModuleAttributesGet('businessCustomer');

    $businessInvoiceItems = sysModuleGetRows(true, 'businessInvoiceItem', $data['id'], 'businessInvoiceId');
    $businessInvoiceItems = sysModuleOrderBy($businessInvoiceItems, 'description');
    $businessInvoiceItemAttrs = sysModuleAttributesGet('businessInvoiceItem');

    if (!$print) {
        echo '<div class="row" style="height:100%"><div class="col-xs-12">';
    }

    echo '
<table width="' . (900) . 'px" height="' . (1200) . 'px" class="faktura">
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black;">
        <td width="33%" style="padding: 0 0 0 10px;"><h4>FAKTÚRA (INVOICE)</h4></td>
        <td width="33%""></td>
        <td width="33%" style="text-align: right; padding: 0 12px 0 0;"><strong>' . sysGetAttributeHtml($data, $attrs['nr']) . '</strong></td>
    </tr>
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 2px solid black;">
        <td colspan="3" style="padding: 0;" >
            <table width="100%" >
                <tr>
                    <td style="padding:10px; border-right: 1px solid black;">
                        <table width="100%">
                            <tr><td colspan="2"><strong>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['name']) . '</strong><br></td></tr>
                            <tr><td colspan="2">' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['street']) . '<br></td></tr>
                            <tr><td colspan="2">' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['city']) . '<br></td></tr>
                            <tr><td colspan="2">' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['country']) . '<br></td></tr>
                            <tr><td colspan="2" style="padding-bottom: 10px;">Spoločnosť zapísaná v Obchodnom registri Okresného súdu Bratislava I., Odd.: Sro, Vl.č.: 169032/B<br></td></tr>
                            <tr><td>IČO(Business ID):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['ico']) . '</td></tr>
                            <tr><td>DIČ(Tax ID):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['dic']) . '<br></td></tr>
                            <tr><td>IČ-DPH(VAT ID):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['icdph']) . '<br></td></tr>
                            <tr><td>TELEFÓN(PHONE):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['phone']) . '<br></td></tr>
                            <tr><td>EMAIL(EMAIL):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['email']) . '<br></td></tr>
                            <tr><td>IBAN(IBAN):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['iban']) . '<br></td></tr>
                            <tr><td>SWIFT(SWIFT):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['swift']) . '<br></td></tr>
                            <tr><td>BANKA(BANK):</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['bank']) . '</td></tr>
                        </table>
                    </td>
                    <td style="vertical-align: top; padding: 0">
                        <table width="100%">
                            <tr>
                                <td style="vertical-align: top; border-right: 1px solid black;">
                                    <table width="100%"><tr><td style="padding: 2px;">Forma úhrady(Payment method):<br />' . sysGetAttributeHtml($data, $attrs['paymentType']) . '</td></tr></table>
                                 </td>
                                <td>
                                    <table width="100%"><tr><td style="padding: 2px;">Variabilný symbol(Variabile symbol):</td><td style="padding: 2px;">' . sysGetAttributeHtml($data, $attrs['vs']) . '</td></tr><tr><td style="padding: 2px;">Konštantny symbol(Constant symbol):</td><td style="padding: 2px;">' . sysGetAttributeHtml($data, $attrs['cs']) . '</td></tr></table>
                                </td>
                            </tr>
                            <tr style="border-top: 1px solid black;"><td colspan="2" style="padding: 5px;">Odoberateľ(Customer)</td></tr>
                            <tr><td colspan="2" style="padding: 10px;">
                                <table width="100%" >
                                    <tr><td colspan="2"><strong>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['name']) . '</strong></td></tr>
                                    <tr><td colspan="2">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['street']) . '</td></tr>
                                    <tr><td colspan="2">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['city']) . '</td></tr>
                                    <tr><td colspan="2" style="padding-bottom: 10px;">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['country']) . '</td></tr>
                                    <tr><td>IČO(Business ID):</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['ico']) . '</td></tr>
                                    <tr><td>DIČ(Tax ID):</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['dic']) . '</td></tr>
                                    <tr><td>IČDPH(VAT reg. no.):</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['icdph']) . '</td></tr>
                                </table>
                            </td></tr>
                        </table>
                    </td>
                </tr>    
            </table>
    </td></tr>
    <tr  style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black;">
        <td style="padding: 5px; border-right: 1px solid black;">Dátum vystavenia(Invoice date):&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['dateCreated']) . '</strong></td>
        <td style="padding: 5px; border-right: 1px solid black;">Dátum dodania(Date of supply):&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['dateDelivered']) . '</strong></td>
        <td style="padding: 5px;">Dátum&nbsp;splatnosti(Payment&nbsp;due&nbsp;date):&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['datePaymentDue']) . '</strong></td>
    </tr>
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black; height: 100%; vertical-align: top;">
        <td colspan="3" style="padding: 20px">
            <table border="1" width="100%">
                <tr><td style="padding: 2px; text-align: center">Popis položky(Description)</td><td style="text-align: center; padding: 2px; ">Množstvo(Amount)</td><td style="text-align: center; padding: 2px; ">MJ(Unit)</td><td style="text-align: center; padding: 2px; ">Cena za MJ(Price per unit)</td><td style="text-align: center; padding: 2px; ">Celková cena(Total)</td></tr>';

    foreach ($businessInvoiceItems as $businessInvoiceItem) {
        echo '
                <tr>
                    <td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($businessInvoiceItem, $businessInvoiceItemAttrs['description']) . '</td>
                    <td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($businessInvoiceItem, $businessInvoiceItemAttrs['amount']) . '</td>
                    <td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($businessInvoiceItem, $businessInvoiceItemAttrs['businessInvoiceItemUnitId']) . '</td>
                    <td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($businessInvoiceItem, $businessInvoiceItemAttrs['perunit']) . '</td>
                    <td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($businessInvoiceItem, $businessInvoiceItemAttrs['total']) . '</td>
                </tr>
            ';
    }

    echo '<tr><td colspan="4" style="text-align:right; padding: 2px; ">Spolu(Total):&nbsp;</td><td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($data, $attrs['total']) . '</td></tr>
            </table>
        </td>
    </tr>
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black; border-bottom: 2px solid black;">
        <td style="vertical-align: top; padding: 5px; border-right: 1px solid black;">Vystavil(Issued by):</td>
        <td style="vertical-align: top; padding: 5px; border-right: 1px solid black;">Prevzal(Received):</td>
        <td style="padding: 0">
            <table width="100%" >
                <tr><td style="border-right: 1px solid black; padding: 5px;">Celková&nbsp;suma(Total):</td><td style="text-align:right;  padding: 5px;">' . sysGetAttributeHtml($data, $attrs['total']) . '</td></tr>
                <tr style="border-top: 1px solid black;"><td style="border-right: 1px solid black; padding: 5px;"><strong>K&nbsp;úhrade(Due&nbsp;total):</strong></td><td style="text-align:right;  padding: 5px;"><h4><strong>' . sysGetAttributeHtml($data, $attrs['total']) . '</strong></h4><span style="font-size:10px;">' . sysGetAttributeHtml($data, $attrs['totalWord']) . '</span></td></tr>
            </table>
        </td>
    </tr>
</table>';

    if ($print) {
        echo '<div class="clearfix"></div>';
        sysPrintHtmlFooter(true);
    } else {
        echo '</div></div></div>
                <div class="row no-print"><div class="col-xs-12">
                    <a href="?a=sysDesignViewPrintEn&origin=' . $_REQUEST['origin'] . '&id=' . $_REQUEST['id'] . '" target="_blank" class="btn btn-success pull-right"><i class="fa fa-print"></i> Print</a>
                    <button class="btn btn-secondary pull-right" style="margin-right: 5px;"><i class="fa fa-download" ></i> Generate PDF</button>
                    <a href="?a=sysDetail&origin=' . $_REQUEST['origin'] . '&uid=' . $_REQUEST['id'] . '" class="btn btn-success pull-right" style="margin-right: 5px;">Back</a>
                </div></div>
            </section>
            <div class="clearfix"></div>';
        sysPrintBlockFooter();
        sysPrintFooter();
    }

}

function sysDesignViewPrintEn()
{
    sysDesignViewEn(true);
}