<?php

function sysCreateAbstractEmptyObject($attrs)
{
    $newObject = array();
    foreach ($attrs as $attr) {
        if ($attr['type'] == 'timestamp') {
            $newObject[0][$attr['db']] = date('Y-m-d H:i') . ':00';
        } elseif (strtolower($attr['name']) == 'id') {
            $newObject[0][$attr['db']] = getUniqueId();
        } else {
            $newObject[0][$attr['db']] = null;
        }
    }
    return $newObject;
}

function sysDesignViewPrint()
{
    sysDesignView(true);
}

function sysDesignView($print = false)
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
        <td width="33%" style="padding: 0 0 0 10px;"><h4>FAKTÚRA</h4></td>
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
                            <tr><td colspan="2" style="padding-bottom: 10px;">Spoločnosť zapísaná v Obchodnom registri <br />Mestského súdu Bratislava III., Odd.: Sro, Vl.č.: 169032/B<br></td></tr>
                            <tr><td>IČO:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['ico']) . '</td></tr>
                            <tr><td>DIČ:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['dic']) . '<br></td></tr>
                            <tr><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['icdph']) . '<br></td><td></td></tr>
                            <tr><td>TELEFÓN:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['phone']) . '<br></td></tr>
                            <tr><td>EMAIL:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['email']) . '<br></td></tr>
                            <tr><td>IBAN:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['iban']) . '<br></td></tr>
                            <tr><td>SWIFT:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['swift']) . '<br></td></tr>
                            <tr><td>BANKA:</td><td>' . sysGetAttributeHtml($businessCompany, $businessCompanyAttrs['bank']) . '</td></tr>
                        </table>
                    </td>
                    <td style="vertical-align: top; padding: 0">
                        <table width="100%">
                            <tr>
                                <td style="vertical-align: top; border-right: 1px solid black;">
                                    <table width="100%"><tr><td style="padding: 2px;">Forma úhrady:</td><td style="padding: 2px;">' . sysGetAttributeHtml($data, $attrs['paymentType']) . '</td></tr></table>
                                 </td>
                                <td>
                                    <table width="100%"><tr><td style="padding: 2px;">Variabilný symbol:</td><td style="padding: 2px;">' . sysGetAttributeHtml($data, $attrs['vs']) . '</td></tr><tr><td style="padding: 2px;">Konštantny symbol:</td><td style="padding: 2px;">' . sysGetAttributeHtml($data, $attrs['cs']) . '</td></tr></table>
                                </td>
                            </tr>
                            <tr style="border-top: 1px solid black;"><td colspan="2" style="padding: 5px;">Odoberateľ</td></tr>
                            <tr><td colspan="2" style="padding: 10px;">
                                <table width="100%" >
                                    <tr><td colspan="2"><strong>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['name']) . '</strong></td></tr>
                                    <tr><td colspan="2">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['street']) . '</td></tr>
                                    <tr><td colspan="2">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['city']) . '</td></tr>
                                    <tr><td colspan="2" style="padding-bottom: 10px;">' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['country']) . '</td></tr>
                                    <tr><td>IČO:</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['ico']) . '</td></tr>
                                    <tr><td>DIČ:</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['dic']) . '</td></tr>
                                    <tr><td>IČDPH:</td><td>' . sysGetAttributeHtml($businessCustomer, $businessCustomerAttrs['icdph']) . '</td></tr>
                                </table>
                            </td></tr>
                        </table>
                    </td>
                </tr>    
            </table>
    </td></tr>
    <tr  style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black;">
        <td style="padding: 5px; border-right: 1px solid black;">Dátum vystravenia:&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['dateCreated']) . '</strong></td>
        <td style="padding: 5px; border-right: 1px solid black;">Dátum dodania:&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['dateDelivered']) . '</strong></td>
        <td style="padding: 5px;">Dátum splatnosti:&nbsp;&nbsp;&nbsp;<strong>' . sysGetAttributeHtml($data, $attrs['datePaymentDue']) . '</strong></td>
    </tr>
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black; height: 100%; vertical-align: top;">
        <td colspan="3" style="padding: 20px">
            <table border="1" width="100%">
                <tr><td style="padding: 2px; text-align: center">Popis položky</td><td style="text-align: center; padding: 2px; ">Množstvo</td><td style="text-align: center; padding: 2px; ">MJ</td><td style="text-align: center; padding: 2px; ">Cena za MJ</td><td style="text-align: center; padding: 2px; ">Celková cena</td></tr>';

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

    echo '<tr><td colspan="4" style="text-align:right; padding: 2px; ">Spolu:&nbsp;</td><td style="text-align: center; padding: 2px; ">' . sysGetAttributeHtml($data, $attrs['total']) . '</td></tr>
            </table>
        </td>
    </tr>
    <tr style="border-left: 2px solid black; border-right: 2px solid black; border-top: 1px solid black; border-bottom: 2px solid black;">
        <td style="vertical-align: top; padding: 5px; border-right: 1px solid black;">Vyhotovil:</td>
        <td style="vertical-align: top; padding: 5px; border-right: 1px solid black;">Prevzal:</td>
        <td style="padding: 0">
            <table width="100%" >
                <tr><td style="border-right: 1px solid black; padding: 5px;">Celková suma:</td><td style="text-align:right;  padding: 5px;">' . sysGetAttributeHtml($data, $attrs['total']) . '</td></tr>
                <tr style="border-top: 1px solid black;"><td style="border-right: 1px solid black; padding: 5px;">Uhradené zálohami:</td><td style="text-align:right; padding: 5px;">0,00 EUR</td></tr>
                <tr style="border-top: 1px solid black;"><td style="border-right: 1px solid black; padding: 5px;">Zostáva uhradiť:</td><td style="text-align:right;  padding: 5px;">' . sysGetAttributeHtml($data, $attrs['total']) . '</td></tr>
                <tr style="border-top: 1px solid black;"><td style="border-right: 1px solid black; padding: 5px;"><strong>K úhrade:</strong></td><td style="text-align:right;  padding: 5px;"><h4><strong>' . sysGetAttributeHtml($data, $attrs['total']) . '</strong></h4><span style="font-size:10px;">' . sysGetAttributeHtml($data, $attrs['totalWord']) . '</span></td></tr>
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
                    <a href="?a=sysDesignViewPrint&origin=' . $_REQUEST['origin'] . '&id=' . $_REQUEST['id'] . '" target="_blank" class="btn btn-success pull-right"><i class="fa fa-print"></i> Print</a>
                    <button class="btn btn-secondary pull-right" style="margin-right: 5px;"><i class="fa fa-download" ></i> Generate PDF</button>
                    <a href="?a=sysDetail&origin=' . $_REQUEST['origin'] . '&uid=' . $_REQUEST['id'] . '" class="btn btn-success pull-right" style="margin-right: 5px;">Back</a>
                </div></div>
            </section>
            <div class="clearfix"></div>';
        sysPrintBlockFooter();
        sysPrintFooter();
    }

}

function sysDesignViewType2($print = false)
{
    $section = sysSectionGet($_REQUEST['origin']);
    $id = escapeQuotes($_REQUEST['id']);
    $module = sysModuleGet($section['id']);
    $attrs = sysModuleAttributesGet($section['id']);
    $template = $module['template'];

    $elements = explodeByCurlyBrackets($template, false);
    $data = array();

    //filling the values of elements one by one
    foreach ($elements as $element) {

        $firstColon = strpos($element['value'], ':');

        $secondColon = strpos($element['value'], ':', ($firstColon + 1));


        $moduleElementStr = substr($element['value'], 0, $firstColon);
        $sectionElementStr = substr($element['value'], 0, $firstColon);
        $attributeElementStr = substr($element['value'], $firstColon + 1);

        $moduleElement = sysModuleGet($moduleElementStr);
        $sectionElement = sysSectionGet($sectionElementStr);
        $attributeElement = sysModuleAttributeGet($sectionElementStr . '-' . $attributeElementStr);

        $dataElement = sysModuleGetRows($sectionElement['type'] == 'abstract', $sectionElement['id'], $id);

        $dataElement = array_pop($dataElement);
        //finally we feed the exported values into array
        $data[$sectionElement['id'] . ':' . $attributeElement['db']] = sysGetAttributeHtml($dataElement, $attributeElement, $sectionElement['type'] == 'abstract');
    }

    //now mix the template with values in curly brackets
    $dataOutput = $module['template'];
    foreach ($data as $key => $item) {
        $dataOutput = str_replace('{' . $key . '}', $item, $dataOutput);
    }

    //printing out
    if ($print) {
        sysPrintHtmlHeader(true);
        echo '<body><div class="wrapper"><section class="invoice">';
    } else {
        sysPrintHeader();
        sysPrintBlockHeader(10, '');
    }

    echo $dataOutput;

    if ($print) {
        echo '</section><div class="clearfix"></div>';
        sysPrintHtmlFooter(true);
    } else {
        echo '</div>
                <div class="row no-print"><div class="col-xs-12">
                    <a href="?a=sysDesignViewPrint&origin=' . $_REQUEST['origin'] . '&id=' . $_REQUEST['id'] . '" target="_blank" class="btn btn-success pull-right"><i class="fa fa-print"></i> Print</a>
                    <button class="btn btn-secondary pull-right" style="margin-right: 5px;"><i class="fa fa-download" ></i> Generate PDF</button>
                    <a href="?a=sysDetail&origin=' . $_REQUEST['origin'] . '&uid=' . $_REQUEST['id'] . '" class="btn btn-success pull-right" style="margin-right: 5px;">Back</a>
                </div></div>
            </section>
            <div class="clearfix"></div>';
        sysPrintBlockFooter();
        sysPrintFooter();
    }

}

function sysDetailGo($sectionName, $id)
{
    $notListedFields = Array(
        'monthly',
        'length',
        'daily'
    );

    $section = sysSectionGet($sectionName);
    $abstract = $section['type'] == 'abstract';
    $isEdit = (strlen($id) > 0);
    $module = sysModuleGet($section['id']);
    $attrs = sysModuleAttributesGet($section['id']);

    $row = array();
    if ($isEdit) {
        $row = sysModuleGetRows($abstract, $section['id'], $id);
        $row = abstractDecode($row, $attrs, $section['id']);
    } else {
        $row = sysCreateAbstractEmptyObject($attrs);
    }

    sysPrintHeader();
    sysPrintBlockHeader('12', ($isEdit ? 'Edit' : 'Insert') . ' ' . $section['id']);
    echo '<script>
            $(function () {
                $("#sysUpsert").click(function() {                
                     var formData = new FormData();
                      if($("#file").length){formData.append("file", $("#file")[0].files[0]);}
                        var attr = "?a=sys' . ($isEdit ? 'Update&uid=' . $id : 'Insert') . '&origin=' . $section['id'] . '";';

    foreach ($attrs as $key => $attr) {
        if (!in_array($key, $notListedFields)) {
            echo '
            formData.append("attr-' . $key . '", $("#' . $key . '").val() );';
        }
    }
    echo '
                    $.ajax({
                        url: (attr),
                        type: "POST",
                        data : formData,
                        processData: false,
                        contentType: false,
                        context: document.body
                    }).done(function(data) {
                        $("#done").slideDown();
                        toastr.info("Operation performed successfully.");                       
                        logConsole(attr);
                        logConsole(data);
                        ' . ($isEdit ? 'window.location.href = "?a=sysDetail&origin=' . $section['id'] . '&uid="+data;' : '') . '
                    });';

    echo '});});
			</script>';

    echo '
    <table id="table-sysDetail" class="table table-bordered table-hover"><tbody>';

    $row = array_pop($row);

    foreach ($attrs as $key => $attr) {
        if (!in_array($key, $notListedFields)) {
            echo '<tr>
                    <td>' . $attr['name'] . ':</td>
                    <td>' . sysGetAttributeHtml($row, $attr, $abstract, true) . '</td>
                  </tr>';
        }
    }
    echo '<tr><td><a class="btn btn-secondary" href="?a=' . $section['id'] . '">Back</a></td><td>';
    if ($isEdit) {
        if (strlen($module['template']) > 0) {
            echo '<a class="btn btn-secondary" href="?a=sysDesignView&origin=' . $section['id'] . '&id=' . $id . '" >Template view</a> ';
        }
        if (sysCanUserWrite($section['id']) && !isModuleEncrypted($section['id'], $attrs)) {
            echo '<button id="sysUpsert" class="btn btn-secondary" style="margin-right: 5px;">Update</button>';
            echo '<a class="btn btn-secondary sysDelete" id="' . $id . '" href="?a=sysDelete&uid=' . $id . '&origin=' . $section['id'] . '" onclick="return confirm(\'Are you sure?\')" >Delete</a>';
        }
    } else {
        echo '<button id="sysUpsert" class="btn btn-secondary">Insert</button>';
    }
    echo '</td></tr></tbody></table>';

    sysPrintBlockFooter();
    sysPrintFooter();

}

function sysDisplayList($section)
{
    $abstract = sysSectionGetType($section) == 'abstract';
    sysPrintHeader();

    echo '<div class="col-12">';
    $attrs = sysModuleAttributesGet($section);
    $rows = sysModuleGetRows($abstract, $section);

    if (array_key_exists('order', $attrs)) {
        $rows = sysModuleOrderBy($rows, 'order', false);
    } else if (array_key_exists('timestamp', $attrs)) {
        $rows = sysModuleOrderBy($rows, 'timestamp', true);
    } else if (array_key_exists('timestampstart', $attrs)) {
        $rows = sysModuleOrderBy($rows, 'timestampstart', true);
    } else if (array_key_exists('name', $attrs)) {
        $rows = sysModuleOrderBy($rows, 'name', false);
    }
    $rows = abstractDecode($rows, $attrs, $section);


    if (sysCanUserWrite($section) && !isModuleEncrypted($section, $attrs)) {
        sysPrintBlockHeader(2);
        echo '<a id="sysInsert" class="btn btn-secondary" href="?a=sysDetail&origin=' . $section . '">Insert</a>';
        sysPrintBlockFooter();
    }

    if (count($rows) > 0) {

        echoBlockHeader();
        if (isModuleEncrypted($section, $attrs)) {
            echo '<a class="btn btn-secondary sysUpdate btn-sm" href="?a=sysModuleLogin&origin=' . $section . '" style="margin-bottom: 10px">Show hidden columns</a>';
        }
        // chart
        /*echo '
        <div class="chart-popup-div"  >
            <div class="chart-popup-div-close">x</div><div id="chart-popup-div-container"></div>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <div class="screenshot current"><button>Screenshot</button></div>
        </div>';*/
        echo '<div class="card">
                
                  <div class="card-body">
                    <table id="table-list" class="table table-bordered table-hover">
                    <thead><tr>';

        if (sysCanUserRead($section)) {
            echo '<td></td>';
        }

        foreach ($attrs as $key => $attr) {
            if (!sysAttributeIsHidden($attr, $section, $attrs)) {
                echo '<th>' . $attr['name'] . ($attr['isChart'] == '1' ? ' <span class="chart" value="' . $key . '" >...</span>' : '') . '</th>';
            }
        }

        echo '</tr></thead><tbody>';

        $durationSum = array();
        $monthlySum = array();
        $previousDate = null;

        foreach ($rows as $row) {

            $row = escapeFromDbToWeb($row);

            if (getKey($attrs, 'length')) {
                $duration = (strtotime($row['timestampstop']) - strtotime($row['timestampstart'])) / 60;
                if (array_key_exists(date('Y-m-d', strtotime($row['timestampstop'])), $durationSum)) {
                    $durationSum[date('Y-m-d', strtotime($row['timestampstop']))] += $duration;
                }
            }
            if (getKey($attrs, 'price')) {
                $time = array_key_exists('timestamp', $row) ? $row['timestamp'] : $row['timestampstart'];
                $date = date('Y-m', strtotime($time));
                if (array_key_exists($date, $monthlySum)) {
                    $monthlySum[$date] += $row['price'];
                }
            }
            if (getKey($attrs, 'daily')) {
                if ($previousDate == null) {
                    $previousDate = date('Y-m-d', strtotime($row['timestampstop']));
                } else {
                    $thisDate = date('Y-m-d', strtotime($row['timestampstop']));
                    if ($thisDate != $previousDate) {
                        $daily = 0;
                        if (array_key_exists($previousDate, $durationSum)) {
                            $daily = $durationSum[$previousDate];
                        }
                        echo getDailyHTML($daily, $previousDate);
                        $previousDate = $thisDate;
                    }
                }
            }
            if (getKey($attrs, 'monthly')) {
                if ($previousDate == null) {
                    $previousDate = date('Y-m', strtotime(array_key_exists('timestamp', $row) ? $row['timestamp'] : $row['timestampstart']));
                } else {
                    $thisDate = date('Y-m', strtotime(array_key_exists('timestamp', $row) ? $row['timestamp'] : $row['timestampstart']));
                    if ($thisDate != $previousDate) {

                        if (array_key_exists($previousDate, $monthlySum)) {
                            echo getMonthlyHTML($previousDate, $monthlySum[$previousDate]);
                        }
                        $previousDate = $thisDate;
                    }
                }
            }

            echo "<tr>";
            if (sysCanUserRead($section)) {
                echo '<td><a class=" btn btn-secondary sysUpdate btn-xs" href="?a=sysDetail&origin=' . $section . '&uid=' . $row['id'] . '"> Detail </a></td>';
            }

            foreach ($attrs as $attr) {
                if (!sysAttributeIsHidden($attr, $section, $attrs)) {
                    echo '<td>' . sysGetAttributeHtml($row, $attr, $abstract) . '</td>';
                }
            }

            echo "</tr>";
        }
        $yearAvg = 0;
        if (getKey($attrs, 'monthly')) {
            $total = 0;
            $i = 0;
            foreach ($monthlySum as $item) {
                $total += $item;
                if ($i < 12) {
                    $yearAvg += $item;
                    $i++;
                }
            }

            $keys = array_keys($monthlySum);

            //echo getMonthlyHTML(end($keys), end($monthlySum));
            if (count($rows) > 0) {
                //echo '<tr><th colspan="99"><span><!-- Total: ' . $total . ' €, -->last 12 months sum: ' . $yearAvg . ' €, last 12 months avg: ' . round($yearAvg / 12) . ' €</span></th></tr><tr><td> </td></tr>';
            }
        }
        echo '</tbody></table>';
        echoTableAutomation('table-list');
        sysPrintBlockFooter();

    }
    sysModuleUploads($section);
    sysPrintFooter();
}

function sysModuleUploads($section)
{
    if (is_dir(UPLOADS . '/' . $section)) {
        $files = array();
        $files = scandir(UPLOADS . '/' . $section);

        echoBlockHeader();
        echo '<table id="table-uploads" class="table table-bordered table-hover"><thead><tr><td>File</td><td>Action</td></tr></thead><tbody>';
        foreach ($files as $file) {
            if (is_dir($file)) continue;
            echo '
                <tr>
                    <td><a href="./?a=sysGetFile&section=' . $section . '&file=' . $file . '" target="_blank">' . $file . '</a></td>
                    <td><a href="./?a=sysDelFile&section=' . $section . '&file=' . $file . '" class=" btn btn-secondary btn-xs" onclick="return confirm(\'Are you sure?\')">Delete</a></td>
                </tr>';
        }
        echo '</tbody></table>';
        //echoTableAutomation('table-uploads');
        sysPrintBlockFooter();
    }
}

function sysDelFile()
{
    $section = escapeQuotes($_REQUEST['section']);
    $section = str_ireplace('..', '', $section);
    $file = escapeQuotes($_REQUEST['file']);
    $file = str_ireplace('..', '', $file);
    unlink(UPLOADS . '/' . $section . '/' . $file);
    header('location: ?a=' . $section);
}

function sysGetFile()
{
    $section = escapeQuotes($_REQUEST['section']);
    $section = str_ireplace('..', '', $section);
    $file = escapeQuotes($_REQUEST['file']);
    $file = str_ireplace('..', '', $file);

    $fileFull = UPLOADS . '/' . $section . '/' . $file;

    if (substr($file, -3) == 'gif') {
        header('Content-Type: image/gif');
    } else if (substr($file, -3) == 'jpg') {
        header('Content-Type: image/jpeg');
    } else if (substr($file, -3) == 'jpeg') {
        header('Content-Type: image/jpeg');
    } else if (substr($file, -3) == 'png') {
        header('Content-Type: image/png');
    } else if (substr($file, -3) == 'pdf') {
        header("Content-type:application/pdf");
    } else if (substr($file, -3) == 'pdf') {
        header("Content-type:application/pdf");
    } else if (substr($file, -3) == 'zip') {
        header("Content-type:application/zip, application/octet-stream");
        header("Content-Disposition:attachment;filename=$file");
    } else {
        //TODO
        header("Content-Disposition:attachment;filename=$file");
    }
    readfile($fileFull);
    die;
}

function transformAbstractListToDynamic($abstractRows)
{

    $dynamicRows = array();
    $currentRow = 0;

    foreach ($abstractRows as $abstractRow) {
        if ($abstractRow['row'] != $currentRow) {
            $currentRow = $abstractRow['row'];
            $dynamicRows[$currentRow]['id'] = $currentRow;
        }
        $attr = substr($abstractRow['sysModuleAttributeId'], strpos($abstractRow['sysModuleAttributeId'], '-') + 1);
        $dynamicRows[$currentRow][$attr] = $abstractRow['value'];
    }
    return $dynamicRows;
}

function sysModuleWhere($rows, $whereAttributeName, $value)
{
    $wheredArray = array();

    foreach ($rows as $key => $row) {
        if ($row[$whereAttributeName] == $value) {
            $wheredArray[$key] = $row;
        }
    }

    return $wheredArray;
}

function sysModuleOrderBy($rows, $orderByKeyName, $desc = false)
{
    $orderedArray = array();
    $tmpArray = array();

    foreach ($rows as $key => $row) {
        $tmpArray[$key] = $row[$orderByKeyName];
    }

    if ($desc) {
        arsort($tmpArray);
    } else {
        asort($tmpArray);
    }

    foreach ($tmpArray as $key => $tmpArrayItem) {
        $orderedArray[$key] = $rows[$key];
    }

    return $orderedArray;
}

function abstractTop($array, $top)
{

    $newArr = array();
    $i = 1;
    foreach ($array as $key => $item) {
        if ($i <= $top) {
            $newArr[$key] = $item;
        }
        $i++;
    }
    return $newArr;
}

function abstractDecode($rows, $attrs, $section)
{

    //find attribute that is ssl type
    foreach ($attrs as $key => $attr) {
        if ($attr['type'] == 'openssl') {
            //go line by line and decode the attribute / create decrypt link
            foreach ($rows as $keyRow => $row) {
                if (!isModuleEncrypted($section, $attrs)) {
                    $rows[$keyRow][$attr['db']] = array_key_exists($attr['db'], $row) ? openssl_decrypt($row[$attr['db']], "AES-128-ECB", base64_decode($_SESSION['module-' . $section])) : null;
                } else {
                    $rows[$keyRow][$attr['db']] = 'encrypted';
                }
            }
        }
    }
    return $rows;
}

function sysModuleGetRows($abstract, $section, $id = null, $attributeWhere = null)
{

    $rows = array();

    if ($abstract) {
        $sql = "SELECT row, sysModuleAttributeId, value FROM `sysModuleValue` where sysModuleId = '$section' " . ((strlen($id) > 0 && $attributeWhere == null) ? "AND row = '$id'" : '') . " order by sysModuleAttributeId";
    } else {
        $attributeWhere = $attributeWhere ?: 'id';
        $sql = "SELECT * FROM `$section` " . (strlen($id) > 0 ? "WHERE $attributeWhere = '$id'" : '') . " order by id desc";

    }

    $result = smart_mysql_query($sql);
    //debug($sql);
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $rows[$i] = $row;
        $i++;
    }

    if (count($rows) > 0) {
        if ($abstract) {
            $rows = transformAbstractListToDynamic($rows);
        }

        $rows = escapeFromDbToWeb($rows);
    }

    if (strlen($attributeWhere) > 0 && $abstract) {
        $rows = sysModuleWhere($rows, $attributeWhere, $id);
    }

    return $rows;
}

function sysGetAttributeHtml($row, $attr, $abstract = true, $edit = false)
{
    if (strlen($attr['db']) > 2 && strtolower(substr($attr['db'], -2)) == 'id') {
        $returnValue = sysGetAttributeIdFormatted($abstract, $attr['db'], $row, $edit);
    } elseif ($attr['db'] == 'length') {
        $returnValue = sysGetAttributeLengthFormatted($row);
    } elseif ($attr['db'] == 'mail') {
        $returnValue = sysGetAttributeMailFormatted($attr, $row, $edit);
    } elseif (strtolower(substr($attr['db'], 0, 3)) == 'url') {
        $returnValue = sysGetAttributeUrlFormatted($attr, $row, $edit);
    } elseif ($attr['db'] == 'monthly') {
        $returnValue = '';
    } else if ($attr['math'] == '1') {
        $returnValue = sysGetAttributeMathFormatted($attr['db'], $row);
    } else if (substr($attr['db'], 0, 9) == 'timestamp') {
        $returnValue = sysGetAttributeTimestampFormatted($attr, $row, $edit);
    }/* else if(key_exists($attr['db'], $row)){
        $returnValue = sysGetAttributeTextFormatted($attr, $row, $edit);
    }*/ else {
        //$returnValue = '<td>'.$attr['db'].'</td>';
        $returnValue = sysGetAttributeTextFormatted($attr, $row, $edit);
    }
    return $returnValue;
}

function sysGetAttributeIdFormatted($abstract, $key, $row, $edit)
{
    $returnString = '';

    if ($edit) {
        $selRows = sysModuleGetRows($abstract, substr($key, 0, -2));
        $returnString .= getHtmlSelect($selRows, $key, $row[$key]);
    } else {
        if (array_key_exists($key, $row) && (is_numeric($row[$key]) || strlen($row[$key]) > 0)) {
            $value = sysModuleGetRows($abstract, substr($key, 0, -2), $row[$key]);

            $returnString .= returnValuesFromRow(array_pop($value));
        }
    }
    return $returnString;


}

function sysGetAttributeLengthFormatted($row)
{
    $duration = (strtotime($row['timestampstop']) - strtotime($row['timestampstart'])) / 60;
    return $duration . ' min';
}

function sysGetAttributeMathFormatted($key, $row)
{
    $returnString = '';
    if (substr($row[$key], 0, 1) == '=') {
        $data = substr($row[$key], 1);
        $attrsToCalculate = explodeByCurlyBrackets($data);
        $calculusString = '';

        foreach ($attrsToCalculate as $attrToCalculate) {
            if (array_key_exists('value', $attrToCalculate)) {
                if (array_key_exists($attrToCalculate['value'], $row)) {
                    $calculusString .= $row[$attrToCalculate['value']];
                }

            }
            if (array_key_exists('operator', $attrToCalculate)) {
                $calculusString .= $attrToCalculate['operator'];
            }
        }
        $calculatedValue = 0;
        $strToEval = '$calculatedValue = ' . $calculusString . ';';
        eval($strToEval);
        $returnString .= '= ' . $calculatedValue;
    } else {
        $returnString .= 'err';
    }

    return $returnString;
}

function sysGetAttributeTimestampFormatted($attr, $row, $edit)
{
    $value = $row[$attr['db']];
    if ($edit) {
        $value = '
        <div class="input-group date" id="reservationdatetime-' . $attr['db'] . '" data-target-input="nearest">
            <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime-' . $attr['db'] . '" id="' . $attr['db'] . '" value="' . $row[$attr['db']] . '" />
            <div class="input-group-append" data-target="#reservationdatetime-' . $attr['db'] . '" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>
        <script>
            $(function () {
                //Date and time picker
                $("#reservationdatetime-' . $attr['db'] . '").datetimepicker({
                 icons: { time: "far fa-clock" }, 
                 format: "YYYY-MM-DD HH:mm:ss",
                });
            });
        </script> ';
    } else {
        $value = strip_tags($value);
    }
    return $value;
}

function explodeByCurlyBrackets($data, $includeDataOutBrackets = true)
{
    $returnArr = array();

    $leftArr = explode('{', $data);

    $i = 0;
    foreach ($leftArr as $leftArrItem) {
        if (strlen($leftArrItem) == 0) continue;

        $dataLeft = substr($leftArrItem, 0, strpos($leftArrItem, '}'));
        if (strlen($dataLeft) > 0) {
            $returnArr[$i]['value'] = $dataLeft;
            $i++;
        }
        //add also what is right to the bracket
        if ($includeDataOutBrackets) {
            $dataRight = substr($leftArrItem, strpos($leftArrItem, '}'));
            if (strlen($dataRight) > 0) {
                $returnArr[$i]['operator'] = $dataRight;
                $i++;
            }
        }

    }

    return $returnArr;
}

function sysGetAttributeTextFormatted($attr, $row, $edit)
{

    //set text type per width or name
    $inputType = 'textarea';
    $lines = 1;
    if (array_key_exists($attr['db'], $row)) {
        $inputType = ((strlen($row[$attr['db']]) > 25) || (strlen($row[$attr['db']]) == 0)) ? 'textarea' : 'text';
        $lines = (count(explode("\n", $row[$attr['db']]))) + 1;
        $lines = $lines > 20 ? 20 : $lines;
        $lines = $lines < 2 ? 2 : $lines;
        //escape quotes
        $row[$attr['db']] = escapeQuotes($row[$attr['db']]);
    }

    if (!$edit && array_key_exists($attr['db'], $row) && strlen($row[$attr['db']]) > 50) {
        $value = substr($row[$attr['db']], 0, 99);
    } else {
        $value = array_key_exists($attr['db'], $row) ? $row[$attr['db']] : null;
    }

    if ($edit && $inputType == 'textarea') {

        $value = '<textarea name="' . $attr['db'] . '" id="' . $attr['db'] . '" class="form-control" rows="' . $lines . '" placeholder="Enter ...">' . (array_key_exists($attr['db'], $row) ? $row[$attr['db']] : null) . '</textarea>';
    } else if ($edit && $inputType == 'text') {
        $value = '<input type="text" name="' . $attr['db'] . '" id="' . $attr['db'] . '" value="' . $row[$attr['db']] . '"  placeholder="-" class="form-control"  />';
    } else if (!$edit) {
        if ($attr['type'] == 'openssl') {
            $val = str_ireplace('"', "'", strip_tags($value));
            if (strlen($val) > 0) {
                $value = '<span class="hider"><button class=" btn btn-secondary btn-xs copyToClipboard" address="' . $val . '" title="' . $val . '"  >Copy</button><span></span></span>';
            } else {
                $value = '<span>-</span>';
            }
        } else {
            $value = '<span>' . strip_tags($value) . '</span>';
        }

    }
    return $value;
}

function sysGetAttributeMailFormatted($attr, $row, $edit)
{
    if ($edit) return sysGetAttributeTextFormatted($attr, $row, $edit);

    $value = null;
    if (array_key_exists($attr['db'], $row)) {
        $value = $row[$attr['db']];
    }
    $mailArr = array();
    if (array_key_exists($attr['db'], $row)) {
        $mailArr = explode(";", $row[$attr['db']]);
    }

    if (count($mailArr) > 0) {
        $value = '';
        foreach ($mailArr as $mail) {
            //if empty string
            if (strlen($mail) < 1) continue;

            //if is not format of mail, surname <mail@adress.com>
            if (strpos($mail, "<") == false) {
                $value .= $mail . ';';

                //normalize mail structure
            } else {
                $firstPos = strpos($mail, '<') + 1;
                $secondPos = strpos($mail, ">");
                $value .= substr($mail, $firstPos, ($secondPos - $firstPos)) . ';';
            }

        }
        //to be on one line
        $value = str_ireplace(" ", "&nbsp;", $value);
    }

    if (strlen($value) > 0) {
        $val = str_ireplace('"', "'", $value);
        $returnString = '
                <div class="btn-group">
                        <a href="mailto:' . $val . '" class=" btn btn-secondary btn-xs" title="' . $val . '">Mail</a><span>&nbsp;</span>
                        <button href="' . $val . '" target="_blank" rel="external" class=" btn btn-secondary btn-xs copyToClipboard" address="' . $val . '" title="' . $val . '">Copy</button>
                </div>';
    } else {
        $returnString = '<span>&nbsp;-</span>';
    }

    return $returnString;
}

function sysGetAttributeUrlFormatted($attr, $row, $edit)
{
    $value = array_key_exists($attr['db'], $row) ? $row[$attr['db']] : null;
    if ($edit) {
        $returnString = sysGetAttributeTextFormatted($attr, $row, $edit);
    } else {
        if (strlen($value) > 0) {
            $returnString = '
                    <div class="btn-group">
                        <a href="' . $value . '" target="_blank" rel="external" class=" btn btn-secondary btn-xs">Go</a>
                        <button href="' . $value . '" target="_blank" rel="external" class=" btn btn-secondary btn-xs copyToClipboard" address="' . $value . '" title="' . $value . '">Copy</button>
                    </div>';
        } else {
            $returnString = '<span>-</span>';

        }

    }

    return $returnString;
}