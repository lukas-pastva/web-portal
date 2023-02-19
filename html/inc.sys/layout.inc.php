<?php

function sysHome()
{
    if ($_SERVER['SERVER_NAME'] == 'portal.mltech.sk') {
        //if( function_exists('businessBazosView') )businessBazosView();
        if (function_exists('smartPostTrackingView')) smartPostTrackingView();
        if (function_exists('smartTogether')) smartTogether();
        if (function_exists('sysCelebrationView')) sysCelebrationView();
    }
    echoSysMenu();
}

function sysPrintHtmlHeader($print = false)
{
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>' . $_SERVER['SERVER_NAME'] . ' - ' . (isset($_COOKIE['cookieUsername']) ? $_COOKIE['cookieUsername'] : '') . ' - ' . escapeQuotes($_REQUEST['a']) . '</title>
  <link rel="shortcut icon" type="image/jpg" href="./facivon.ico"/>
  
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="./css/style.css">';

    if (!$print) {
        echo '
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- <link rel="stylesheet" href="./css/font-awesome.min.css"> -->
  <!-- Toastr -->
  <link rel="stylesheet" href="./plugins/toastr/toastr.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="./plugins/daterangepicker/daterangepicker.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="./plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="./plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="./plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="./plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- CodeMirror -->
  <link rel="stylesheet" href="./plugins/codemirror/lib/codemirror.css">
  <link rel="stylesheet" href="./plugins/codemirror/theme/monokai.css">
  <!-- jQuery -->
  <script src="./plugins/jquery/jquery.min.js"></script>   
  <script src="js/js.js?cache=2"></script>';
    }
    echo '
  </head>';
}

function sysPrintHeader()
{
    sysPrintHtmlHeader();
    $user = sysUserGet($_COOKIE['cookieUsername']);
    echo '
  <body class="hold-transition sidebar-mini text-sm ' . (sysUserGetSetting(1)) . ' ' . (sysUserGetSetting(4)) . '">
    <div class="wrapper">
    
      <nav class="main-header navbar navbar-expand ' . (sysUserGetSetting(2)) . '">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" id="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>
        
           <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
       <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge NotificationsMenuCount">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right NotificationsMenu">          
          <a href="?a=sysLogDebug" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item"><i class="fas fa-envelope mr-2"></i> User<span class="float-right text-muted text-sm">' . $_COOKIE['cookieUsername'] . '</span></a>
          <div class="dropdown-divider"></div><a href="#" class="dropdown-item"><i class="fas fa-envelope mr-2"></i> Email<span class="float-right text-muted text-sm">' . $user['mail'] . '</span></a>
          <div class="dropdown-divider"></div><a href="#" class="dropdown-item"><i class="fas fa-envelope mr-2"></i> Online users<span class="float-right text-muted text-sm">' . sysCountOnlineUsers() . '</span></a>
          <div class="dropdown-divider"></div><a href="?a=logout" class="dropdown-item dropdown-footer">Log out</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>            
  <!-- /.navbar -->
      
  <!-- Main Sidebar Container -->                
  <aside class="main-sidebar elevation-4 ' . (sysUserGetSetting(3)) . '">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link text-sm">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">' . $_SERVER['SERVER_NAME'] . '</span>
    </a>
  
    <!-- Sidebar -->
    <div class="sidebar">
      
      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
    
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">';

    $mainMenuItems = sysSectionsGet('root');
    foreach ($mainMenuItems as $key => $item) {
        $subItems = sysSectionsGet($item['id']);
        $active = isSubmenuSelected($item['id'], $_REQUEST['a']);
        echo '
                <li class="nav-item ' . ($active ? 'menu-open' : '') . '">
                  <a href="' . (count($subItems) > 0 ? '#' : '?a=' . $item['id']) . '" class="nav-link' . ($active ? ' active' : '') . '">
                  <i class="nav-icon fas  ' . ($item['icon'] == '' ? 'fa-copy' : $item['icon']) . '"></i> 
                  <p>' . $item['name'] . '<i class="fas fa-angle-left right"></i></p> 
                </a>
                ';


        if (count($subItems) > 0) {
            echo '
                <ul class="nav nav-treeview">';
            foreach ($subItems as $key => $subItem) {
                $icon = $subItem['icon'] == '' ? 'fa-circle-o' : $subItem['icon'];
                $type = sysSectionGetType($subItem['id']);
                $typeIcon = '';
                if ($type == 'abstract') {
                    $typeIcon = 'text-warning';
                } elseif ($type == 'dynamic') {
                    $typeIcon = 'text-info';
                } elseif ($type == 'script') {
                    $typeIcon = 'text-danger';
                }
                echo '
                  <li class="nav-item">
                      <a href="?a=' . $subItem['id'] . '" class="nav-link' . (($subItem['id'] == $_REQUEST['a']) ? ' active' : '') . '">
                        <i class="far fa ' . $icon . ' ' . $typeIcon . ' nav-icon"></i>
                        <p>' . $subItem['name'] . '</p>
                      </a>
                  </li>';

            }
            echo '
              </ul>';
        }
        echo '
            </li> ';
    }

    $parent = getSectionParent($_REQUEST['a']);
    echo '
           </ul>
     </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
       
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">' . sysSectionGetName($_REQUEST['a']) . '</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              ' . (is_array($parent) ? '<li class="breadcrumb-item">' . $parent['name'] . '</li>' : '') . '
              <li class="breadcrumb-item active">' . sysSectionGetName($_REQUEST['a']) . '</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    
        <!-- Main content -->
        <section class="content">
        <div class="row">';

}

function sysPrintFooter($print = false)
{

    echo '</div></div></div>';
    sysPrintHtmlFooter($print);
}

function sysPrintHtmlFooter($print = false)
{

    if (!$print) {
        echo '  
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-light">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
';
        echo '  
    
    <script type="text/javascript">';

        if (sysCanUserRead('sys')) {
            echo '//heartbeat
        setInterval(function() {
          var attr = "index.php?a=sysCron";
          $.ajax({
            url: attr
          }).done(function(data) {
              if(data.length > 0){
                logConsole("Heartbeat: "+data);                  
              }
          });
        }, (30 * 1000));';
        }
        echo ' 
        $(document).ready(function() {
        
            $(".dataTables_filter INPUT").keyup(function(){
                var new_value = $(this).val();                
                var attr = "index.php?a=sysUserSetSetting&nr=5&value=" + new_value;
                logConsole("Changing settings nr: 5 to: " + new_value);
                ajax(attr, false);
            });	       
             
            $("#pushmenu").click(function(){
                var new_value = $(".sidebar-mini").hasClass("sidebar-collapse")?"":"sidebar-collapse";
                var attr = "index.php?a=sysUserSetSetting&nr=4&value=" + new_value;
                logConsole("Changing settings nr: 4 to: " + new_value);
                ajax(attr, false);
            });	
            
            $(".copyToClipboard").click(function () {
                // $(this).effect( "shake", { direction: "right", times: 1, distance: 5}, 200 );
               
                let str = $(this).attr("address");
                const el = document.createElement("textarea");
                el.value = str;
                el.setAttribute("readonly", "");
                el.style.position = "absolute";
                el.style.left = "-9999px";
                document.body.appendChild(el);
                el.select();
                document.execCommand("copy");
                document.body.removeChild(el);
            });
    });
    </script>';

    }

    if (!$print) {
        echo '
<!-- REQUIRED SCRIPTS -->
<!-- Bootstrap 4 -->
<script src="./plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="./plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="./plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="./plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="./plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="./plugins/jszip/jszip.min.js"></script>
<script src="./plugins/pdfmake/pdfmake.min.js"></script>
<script src="./plugins/pdfmake/vfs_fonts.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="./plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="./plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="./dist/js/adminlte.min.js"></script>
<script src="./dist/js/demo.js"></script>

<!-- date-range-picker -->
<!-- InputMask -->
<script src="./plugins/moment/moment.min.js"></script>
<script src="./plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="./plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- CodeMirror -->
<script src="./plugins/codemirror/lib/codemirror.js"></script>
<script src="./plugins/codemirror/mode/clike/clike.js"></script>';
    }

    echo '
</body>
</html>';

}

function sysPrintBlockHeader($size, $name = '')
{
    echo '<div class="col-lg-' . $size . '">
            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">' . $name . '</h3>
                </div>
              </div>
              <div class="card-body">';
}

function sysPrintBlockFooter()
{

    echo '</div></div></div>';
}

function echoTableAutomation($id)
{
    echo '<script type="text/javascript">                    
                  $(function () {
                    $("#' . $id . '").DataTable({
                      "responsive": true, 
                      "lengthChange": false, 
                      "autoWidth": false,
                      "buttons": ["copy", "excel", "pdf", "colvis"],
                      "pageLength": 50,
                      "oSearch": {"sSearch": "' . (sysUserGetSetting(5)) . '" }
                    }).buttons().container().appendTo(\'#' . $id . '_wrapper .col-md-6:eq(0)\');                   
                  });
               </script>';
}

function echoBlockHeader()
{
    echo '<div class="col-lg-12">
            <div class="card">
              <div class="card-body">';
}

function echoBlockFooter()
{
    echo '</div></div></div>';
}

function getDailyHTML($daily, $previousDate)
{
    return '<tr><th class="daily" colspan="99" value="' . $daily . '"><span>' . $previousDate . ' daily: ' . $daily . 'min / ' . round(($daily / 60), 2) . ' hod</span><span class="chart-popup" value="daily" ></span></th></tr>';
}

function getMonthlyHTML($previousDate, $monthly)
{
    return '<tr><th class="monthly" colspan="99" value="' . $monthly . '"><span>' . $previousDate . ' monthly: ' . $monthly . ' €</span><span class="chart-popup" value="monthly" ></span></th></tr><tr><td> </td></tr>';
}

function sysModuleWizzard()
{
    //16241167174572444 = text
    //16241167303753468 = timestamp
    //16242704331859590 = openssl
    $attrs['timestamp'] = '16241167303753468';
    $attrs['name'] = '16241167174572444';
    $attrs['value'] = '163628784342392225';
    //$attrs['timestampstart'] = '16241167303753468';
    //$attrs['timestampend'] = '16241167303753468';
    //$attrs['fullname'] = '16241167174572444';
    //$attrs['file'] = '16241167174572444';
    //$attrs['type'] = '16241167174572444';
    //$attrs['text'] = '16241167174572444';
    //$attrs['description'] = '16241167174572444';
    //$attrs['price'] = '16241167174572444';
    //$attrs['monthly'] = '16241167174572444';
    //$attrs['length'] = '16241167174572444';
    //$attrs['user'] = '16242704331859590';
    //$attrs['pass'] = '16242704331859590';
    //$attrs['url'] = '16242704331859590';


    sysPrintBlockHeader(6, 'Add module here.');

    echo '<script><!--
    $(function () {
        
        const attrDb = [];';
    $moduleAttributeTypes = sysModuleGetRows(false, 'sysModuleAttributeType');
    foreach ($moduleAttributeTypes as $moduleAttributeType) {
        echo '
        //' . $moduleAttributeType['name'] . '
        attrDb["' . $moduleAttributeType['id'] . '"] = "' . $moduleAttributeType['db'] . '";
        ';
    }

    echo ' 
    
        $(".module-attr-button").click(function() 
        {
            $(this).toggleClass("btn-primary");
            $(this).toggleClass("btn-secondary");
        });
        $(".add-attribute").click(function() {
            var name = $(".add-attribute-text").val();
 
            var new_button = $(\'' . getButtonSecondary('\'+ name +\'-button', '\'+ name +\'') . '\');
            new_button.click(function() {
                $(this).toggleClass("btn-primary");
                $(this).toggleClass("btn-secondary");
            });
            $(\'.module-attributes\').append(new_button);
            new_button.show(\'normal\');
            
            var new_select = $(\'' . getSelectBox('sysModuleAttributeType', 'name', null, '\'+name+\'-select') . '\');
            new_select.click(function() {
                $(this).toggleClass("btn-primary");
                $(this).toggleClass("btn-secondary");
            });
            $(\'.module-attributes\').append(new_select);
            new_select.show(\'normal\');
        });
                  
      
      $(".create").click(function() {
          if($("#modulename").val()=="" || $("#modulename-system").val()==""){
            alert("Fill in Name.");
          }else{                        
            var moduleNameSystem = $("#modulename-system").val();            
            if(confirm("This will run SQL queries.")) {
                logConsole("creating section");
                var root = $(".root").val();
                var attr = "?a=sysInsert&origin=sysSection&attr-id="+moduleNameSystem+"&attr-name="+$("#modulename").val()+"&attr-sysSectionId="+root+"&attr-type="+$("#moduletype").val()+"&attr-ordering=110";
                
                $.ajax({url: attr}).done(function(data) {
                    logConsole("creating module");
                    var attr = "?a=sysInsert&origin=sysModule&attr-id="+moduleNameSystem;
                    logConsole(attr);
                    
                    $.ajax({url: attr}).done(function(data) {
                        logConsole("enable section");
                        var attr = "?a=sysInsert&origin=sysUserSection&attr-sysUserSectionTypeid=3&attr-sysSectionId="+moduleNameSystem+"&attr-sysUserid=' . $_COOKIE['cookieUsername'] . '";
                        logConsole(attr);
                        
                        $.ajax({url: attr}).done(function(data) {
                            var attrArr = new Array();
                            $(".module-attr-button.btn-primary").each(function(){
                                attrArr.push($(this));
                            });
                            createAttibuteRecursive(attrArr, moduleNameSystem);
                            
                            if( $("#moduletype").val() == "dynamic"){
                                //alert("Execute next popup in DB");
                                var attributes = "";
                                var sqlString = "CREATE TABLE IF NOT EXISTS  `"+moduleNameSystem+"` (\r\n`id` bigint(17) NOT NULL AUTO_INCREMENT,\r\n";
                                $(".module-attr-button.btn-primary").each(function(){                                    
                                    var id = $("#"+$(this).attr("id").slice(0, -7)+"-select").val();
                                    var name = $(this).text();
                                    sqlString += "`"+name+"` "+attrDb[id] + ",\r\n";
                                    attributes += name+":"+id + ",";
                                });
                                attributes = attributes.slice(0, -1);
                                sqlString += "PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
                                
                                console.log(sqlString);
                                
                                //Create Java Module
                                var formData = new FormData();
                                formData.append(\'module\', moduleNameSystem);
                                formData.append(\'attributes\', attributes);
                                    $.ajax({
                                        url: ("?a=sysToolsJavaModuleGeneratorGo"),
                                        type: "POST",						  
                                        data : formData,	  
                                        processData: false,
                                        contentType: false,
                                        context: document.body					 
                                    }).done(function(data) {
                                        //console.log(data);
                                        
                                        var blob = new Blob([data], { type: "application/octetstream" });
                                        var url = window.URL || window.webkitURL;
                                        link = url.createObjectURL(blob);
                                        var a = $("<a />");
                                        a.attr("download", moduleNameSystem.toLowerCase()+".zip");
                                        a.attr("href", link);
                                        $("body").append(a);
                                        a[0].click();
                                        $("body").remove(a);
                                        //download(moduleNameSystem+".zip", atob(data));
                                    });
                                
                            }
                            
                        });
                        
                    });
                    
                });
                
            }
          }
      });     
    }); 
    
    function createAttibuteRecursive(attrArr, moduleNameSystem){        
        if(attrArr.length==0){      
            logConsole("ALL done!");
        }else{
            var element = attrArr.shift();
            
            logConsole("creating attribute: "+element.attr("id").slice(0, -7)) ;
            var id = element.attr("id").slice(0, -7);
            var name = id.charAt(0).toUpperCase() + id.slice(1);
            var type = $("#"+id+"-select").val();
            var attr = "?a=sysInsert&origin=sysModuleAttribute&attr-id="+moduleNameSystem+"-"+id+"&attr-name="+name+"&attr-sysModuleId="+moduleNameSystem+"&attr-sysModuleAttributeTypeId="+type+"&attr-def=&attr-db="+id+"&attr-isChart=0&math=0";
            //logConsole(attr);
            $.ajax({url: attr}).done(function(data) {
                createAttibuteRecursive(attrArr, moduleNameSystem);
            });
        }
    }
    -->
    </script>
        
    <table id="example2" class="table table-bordered table-striped">            
        <tr><td>Module system Name</td><td><input type="text" class="modulename-system" id="modulename-system" name="modulename-system" /></td></tr>
        <tr><td>Module name</td><td><input type="text" class="modulename" id="modulename" name="modulename" /></td></tr>
        <tr><td>Module type (dynamic, abstract)</td><td><input type="text" class="moduletype" id="moduletype" name="moduletype" value="abstract" /></td></tr>';

    $roots = sqlGetRows("select * from sysSection where sysSectionId = 'root' order by ordering asc");
    echo '<tr><td>Parent</td><td><select class="root form-control">';
    foreach ($roots as $root) {
        echo '<option value="' . $root['id'] . '">' . $root['name'] . '</option>';
    }
    echo '</select></td></tr><tr><td>Attributes</td><td>';


    echo '<div class="module-attributes">';
    foreach ($attrs as $key => $attr) {
        echo getButtonSecondary($key . '-button', $key);
        echo getSelectBox('sysModuleAttributeType', 'name', $attr, $key . '-select');
        echo '<br />';
    }
    echo '
        </div>
        <br >
        <input type="text" class="add-attribute-text" />
        <button class="btn btn-secondary add-attribute">Add attribute</button>
    </td></tr>
    <tr><td>
        <button class="btn btn-secondary create">Create</button>
    </td></tr></tbody></table>';

    sysPrintBlockFooter();
}

function getButtonSecondary($elementId, $value)
{
    return '<button class="module-attr-button btn btn-block btn-xs btn-secondary" id="' . $elementId . '" >' . $value . '</button> ';
}

function getSelectBox($table, $nameOfAttribute, $selectedId, $elementId)
{
    $items = sqlGetRows('SELECT * from ' . $table . ' order by ' . $nameOfAttribute . ' asc');
    $return = '<select class="' . $table . ' form-control" id="' . $elementId . '"><option></option>';
    foreach ($items as $item) {
        $return .= '<option value="' . $item['id'] . '" ' . ($selectedId == $item['id'] ? 'selected="selected"' : '') . '>' . $item[$nameOfAttribute] . '</option>';
    }
    return $return . '</select>';

}

function sysDisplayChart($type, $span = null)
{
    $timestampFrom = '24';
    $timestampFromType = 'HOUR';
    $timestampFormat = 'M-d H:i';
    $timestampFormatSql = '%Y-%m-%d %k';
    if ($span < 3) {
        $timestampFrom = ($span + 1) * 24;
        $timestampFromType = 'HOUR';
        $timestampFormat = 'M-d H:i';
        $timestampFormatSql = '%Y-%m-%d %k:%i';
    }
    if ($span == 3) {
        $timestampFrom = 7 * 24;
        $timestampFromType = 'HOUR';
        $timestampFormat = 'M-d H:i';
        $timestampFormatSql = '%Y-%m-%d %k:%i';
    }
    if ($span == 4) {
        $timestampFrom = '1';
        $timestampFromType = 'MONTH';
        $timestampFormat = 'Y-M-d';
        $timestampFormatSql = '%Y-%m-%d';
    }
    if ($span == 5) {
        $timestampFrom = '1';
        $timestampFromType = 'YEAR';
        $timestampFormat = 'Y-M-d';
        $timestampFormatSql = '%Y-%m-%d';
    }

    echo getMaxMinValue($timestampFrom, $timestampFromType, $type, true) . ' | ' . getMaxMinValue($timestampFrom, $timestampFromType, $type, false) . '<br />';
    /*
        $result = smart_mysql_query("SELECT DATE_FORMAT(timestamp, '" . $timestampFormatSql . "') time, AVG(val) val from smartsensordata th join smartenumsensor s on th.smartenumsensorid = s.id where timestamp > DATE_SUB(NOW(),INTERVAL " . $timestampFrom . " " . $timestampFromType . ") and s.name = '" . $type . "' GROUP BY DATE_FORMAT(timestamp, '" . $timestampFormatSql . "') order by timestamp asc");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date = new DateTime($row["time"]);
                $dateStr = $date->format($timestampFormat);
                $val = eval(getShiftForSensorByName($type, $row["val"]));
                echo "['" . $dateStr . "'," . $val . "],";
            }
        }

        $min = $val = eval(getShiftForSensorByName($type, getMinValueSql($timestampFrom, $timestampFromType, $type)));
        $min = $min < 0 ? $min : 0;

        */
}

function echoSysMenu()
{

    echoBlockHeader();
    $menuItems = sysSectionsGet('root');
    echo '<ul>';

    foreach ($menuItems as $item) {
        echo '<li>';
        echo '<a href="?a=' . $item['id'] . '">
                <span>' . $item['name'] . '</span>
              </a>';

        $subItems = sysSectionsGet($item['id']);
        if (count($subItems) > 0) {
            echo '<ul>';
            foreach ($subItems as $subItem) {
                echo '<li><a href="?a=' . $subItem['id'] . '">' . $subItem['name'] . '</a></li>';
            }
            echo '</ul>';
        }
        echo "</li> ";
    }
    echo '</ul>';
    echoBlockFooter();

}

function getHtmlSelect($values, $id, $selectedValue)
{
    $returnString = '';
    $returnString .= '<select name="' . $id . '" id="' . $id . '" class="form-control"><option></option>';

    foreach ($values as $key => $value) {

        $returnString .= '<option value="' . $value['id'] . '" ' . ($value['id'] == $selectedValue ? ' selected="selected"' : '') . '>';
        $returnString .= returnValuesFromRow($value);
        $returnString .= '</option>';

    }

    $returnString .= '</select>';
    return $returnString;
}

function returnValuesFromRow($value)
{
    $returnString = '';
    if (is_array($value) && count($value) > 0) {
        if (array_key_exists('name', $value)) {
            $returnString = $value['name'];
        } elseif (array_key_exists('fullname', $value)) {
            $returnString = $value['fullname'];
        } elseif (array_key_exists('description', $value)) {
            $returnString = $value['description'];
        } elseif (array_key_exists('nr', $value)) {
            $returnString = $value['nr'];
        } elseif (array_key_exists('id', $value)) {
            $returnString = $value['id'];
        } else {
            foreach ($value as $key => $item) {
                $returnString .= $key . ': ' . $item . " / ";
            }

        }
    }
    return $returnString;

}

function pageLogin()
{
    $message = '';
    if (array_key_exists('user', $_REQUEST) && array_key_exists('user', $_REQUEST)) {
        $message = doLogin(escapeQuotes($_REQUEST['user']), escapeQuotes($_REQUEST['pass']));
    }


    sysPrintHtmlHeader();
    echo '<body class="login-page"><script><!--
			$(function () {
				$("#user").focus();	
				$("#loginbutton").click(function() {	
					loginFunction();
					return false;
				});
				$(document).keypress(function(e) {
				    if(e.which == 13) {loginFunction();}
				});				
				function loginFunction(){				
					if($("#pass").val().length < 1){				
						$("#pass").focus();			
					}else{
						$("#loginform").submit();
					}
				}
				
			});			
			--></script>
    <div class="login-box">
      <div class="login-logo">
        <a href="./?a=sysHome"><b>' . $_SERVER['SERVER_NAME'] . '</b> Login</a>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg">' . $message . '</p>       
        <form method="post" class="login" id="loginform">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Userame" name="user" id="user"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="pass" id="pass"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">    
            </div>
            <div class="col-xs-4">
              <button type="submit" class="btn btn-secondary btn-block btn-flat"  id="loginbutton">Sign In</button>
            </div></div>
        </form>
       </div>
      </div>';

    sysPrintFooter();
}

function sysModuleLogin()
{
    sysPrintHtmlHeader();
    echo '<body class="login-page"><script><!--
			$(function () {			 
				$("#loginbutton").click(function(){
				    var attr = "?a=sysModuleLoginGo&origin=' . $_REQUEST['origin'] . '&pass="+$("#pass").val();
                    $.ajax({url: attr}).done(function(data) {			 
                        window.location.href = "?a=' . $_REQUEST['origin'] . '";
				    });
				    return false;
			    });			
			});
			--></script>
    <div class="login-box">
      <div class="login-logo">
        <a href="./?a=' . $_REQUEST['origin'] . '"><b>' . $_REQUEST['origin'] . '</b> module decrypt</a>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg">Write down passphrase for decrypting the data.</p>       
        <form method="post" class="login" id="loginform">         
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Passphrase" name="pass" id="pass"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">    
            </div>
            <div class="col-xs-4">
              <button type="submit" class="btn btn-secondary btn-block btn-flat"  id="loginbutton">Decrypt</button>
            </div></div>
        </form>
       </div>
      </div>';

    sysPrintFooter();
}

function sysModuleLoginGo()
{
    $_SESSION['module-' . $_REQUEST['origin']] = base64_encode($_REQUEST['pass']);
    die;
}
