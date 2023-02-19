<?php

function sysDetailInvoicePrint(){
    sysPrintHtmlHeader(true);
    echo '<body><div class="wrapper"><section class="invoice">';
    printInvoice();
    echo '<section class="invoice">';
    sysPrintHtmlFooter(true);
}

function sysDetailInvoice(){

    sysPrintHeader();
    echo '<section class="invoice">';
    printInvoice();

    echo '
          <div class="row no-print"><div class="col-xs-12">              
              <a href="invoice-print.php" target="_blank" class="btn btn-success pull-right"><i class="fa fa-print"></i> Print</a>
              <button class="btn btn-secondary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
            </div></div></section><div class="clearfix"></div>';
    sysPrintHtmlFooter();
}


function printInvoice(){

    echo '        
        <div class="row"><div class="col-xs-12"><h2 class="page-header">
          <i class="fa fa-globe"></i> AdminLTE, Inc.<small class="pull-right">Date: 2/10/2014</small>
        </h2></div></div>
        <div class="row invoice-info">
          <div class="col-sm-4 invoice-col">
            From
            <address>
              <strong>Admin, Inc.</strong><br>
              795 Folsom Ave, Suite 600<br>
              San Francisco, CA 94107<br>
              Phone: (804) 123-5432<br/>
              Email: info@almasaeedstudio.com
            </address>
          </div><!-- /.col -->
          <div class="col-sm-4 invoice-col">
            To
            <address>
              <strong>John Doe</strong><br>
              795 Folsom Ave, Suite 600<br>
              San Francisco, CA 94107<br>
              Phone: (555) 539-1037<br/>
              Email: john.doe@example.com
            </address>
          </div><!-- /.col -->
          <div class="col-sm-4 invoice-col">
            <b>Invoice #007612</b><br/>
            <br/>
            <b>Order ID:</b> 4F3S8J<br/>
            <b>Payment Due:</b> 2/22/2014<br/>
            <b>Account:</b> 968-34567
          </div></div>

        <div class="row"><div class="col-xs-12 table-responsive"><table class="table table-striped"><thead>
          <tr><th>Qty</th><th>Product</th><th>Serial #</th><th>Description</th><th>Subtotal</th></tr>
        </thead><tbody> 
        
                <tr>
                  <td>1</td>
                  <td>Call of Duty</td>
                  <td>455-981-221</td>
                  <td>El snort testosterone trophy driving gloves handsome</td>
                  <td>$64.50</td>
                </tr> 
                
        </tbody></table></div></div>
        <div class="row"><div class="col-xs-8"></div>          
          <div class="col-xs-4"><p class="lead">Amount Due 2/22/2014</p><div class="table-responsive"><table class="table">
            <tr>
              <th style="width:50%">Subtotal:</th>
              <td>$250.30</td>
            </tr>
            <tr>
              <th>Tax (9.3%)</th>
              <td>$10.34</td>
            </tr>
            <tr>
              <th>Shipping:</th>
              <td>$5.80</td>
            </tr>
            <tr>
              <th>Total:</th>
              <td>$265.24</td>
            </tr>
          </table></div></div></div>
    ';
}
