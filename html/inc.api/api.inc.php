<?php

function acioprReporting()
{
    echo "\nACIOPR Reporting cron\n";
    //select all from reporting table and go one by one
    $result = smart_mysql_query($sql = "
            SELECT t.name as type, r.name as name, r.timestamp as timestamp, r.id as id, r.query as query, s.seckey as seckey, s.url as url, f.value as frequency, a.name as action, l.name as sqlServer 
            FROM acioprReportingReports r 
            LEFT JOIN acioprReportingServer s on s.id = r.acioprReportingServerId 
            LEFT JOIN acioprReportingFrequency f on f.id = r.acioprReportingFrequencyId 
            LEFT JOIN acioprReportingAction a on a.id = r.acioprReportingActionId
            LEFT JOIN acioprReportingType t on t.id = r.acioprReportingTypeId
            LEFT JOIN acioprReportingServerSql l on l.id = r.acioprReportingServerSqlId                        
            where (FROM_UNIXTIME(UNIX_TIMESTAMP(r.timestamp)+(f.value*60))) < (now());");
    if (($result->num_rows != false) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $row = escapeFromDbToWeb($row);

            $rand = getUniqueId();
            $query = $row['query'];
            $query = str_ireplace('###Y', date(date('Y')), $query);
            $query = str_ireplace('###y', date(date('y')), $query);
            $query = str_ireplace('###m', date(date('m')), $query);
            $query = str_ireplace('###d', date(date('d')), $query);
            $cron = $row['url']
                . '&key=' . $row['seckey']
                . '&action=' . $row['action']
                . '&query=' . base64_encode($query)
                . '&runid=' . base64_encode($rand)
                . '&name=' . base64_encode($row['name'])
                . '&type=' . base64_encode($row['type'])
                . '&sqlServer=' . base64_encode(strlen($row['sqlServer']) > 0 ? $row['sqlServer'] : 'null');

            //update the DB, that the cron was ran
            smart_mysql_query($sql = "UPDATE acioprReportingReports SET timestamp = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $row['id'] . "'");
            logDebug('Calling cron ' . $cron);
            $return = callCurl($cron);
            logDebug('Cron return data:' . $return['data'] . ', err: ' . print_r($return['err'], true));
            echo $return['data'];

        }
    }

}

function sysToolsLogBeautifier()
{

    echo '<script><!--            
			$(function () {
				$("#go").click(function(){
                    $(document.body).css({\'cursor\' : \'wait\'});
                    var formData = new FormData();
                    formData.append(\'data\', $("#input").val());
                        $.ajax({
    						url: ("?a=sysToolsLogBeautifierGo"),
    						type: "POST",						  
    			  			data : formData,	  
           					processData: false,
           					contentType: false,
    					  	context: document.body					 
						}).done(function(data) {
                            $(document.body).css({\'cursor\' : \'default\'});
							$("#output").html(data)
						});
				});
			});
			--></script>';

    sysPrintBlockHeader(10, 'AWS log beautifier');

    echo ' 
            <textarea id="input" class="form-control" rows="12" placeholder="Enter ..."></textarea>
            
            <br />
            <a id="go" class="btn btn-secondary">Go</a>
            <br />
            <br />
            <textarea id="output" class="form-control" rows="12" placeholder="Click Go ..."></textarea>
           ';

    sysPrintBlockFooter();

}

function sysToolsLogBeautifierGo()
{
    $text = $_REQUEST['data'];

    $text = str_ireplace('???', ' ', $text);
    $text = str_ireplace("\t", '', $text);
    $text = str_ireplace('  ', ' ', $text);
    $text = str_ireplace(' - ', ' ', $text);
    $text = preg_replace("/[\n\r]/", "", $text);
    $text = preg_replace('/(\w+)-(\d+)-(\d+)T(\d+):(\d+):(\d+).(\d+)Z/i', '', $text);
    $text = str_ireplace('[]', ' ', $text);
    $text = str_ireplace('info:', '
    info:', $text);
    $text = str_ireplace('error:', '
    error:', $text);

    echo 'There was an error in Shell script log: 
```
' . $text . '
```
';
}

function sysToolsAwxPassGenerator()
{

    echo '<script><!--
            $(function() {$( "#tabs" ).tabs();});
			$(function () {				 
                $("#go").click(function(){
                    $(document.body).css({\'cursor\' : \'wait\'});
					var attr = "?a=sysToolsAwxPassGeneratorGo";
					$.ajax({
							url: (attr),
							context: document.body
						}).done(function(data) {
                            $(document.body).css({\'cursor\' : \'default\'});
							$("#output").html(data)
						});
				});
			});
			--></script>';

    sysPrintBlockHeader(10, 'AWS password file generator');

    echo 'This will generate AWX files. Credentials also schema files will be below.';
    echo '
            
            <br />
            <a id="go" class="btn btn-secondary">Go</a>
            <br />
            <br />
            <textarea id="output" class="form-control" rows="12" placeholder="Click Go ..."></textarea>
           ';

    sysPrintBlockFooter();
}

function sysToolsAwxPassGeneratorGo()
{

    $dir = './it-passwd';
    $dirCredentials = './it-credentials';
    $dirData = './it-schema';

    $files = scandir($dir);
    // echo 'ansible-vault encrypt';
    $putty = '';
    $schemas = '';
    for ($i = 0; $i < count($files); $i++) {
        if ($i < 2)
            continue;

        $usersFile = file_get_contents($dir . '/' . $files[$i]);
        $usersFile = str_ireplace("\r\n", "", $usersFile);
        $usersLines = explode('name="', $usersFile);

        for ($j = 0; $j < count($usersLines); $j++) {
            if ($j < 1)
                continue;
            $usersLine = $usersLines[$j];

            $user = substr($usersLine, 0, strpos($usersLine, '"'));

            $passwordPosition = strpos($usersLine, 'password="') + 10;
            $passwordString = substr($usersLine, $passwordPosition);
            $password = substr($passwordString, 0, (strpos($passwordString, '"')));
            $password = str_ireplace("&amp;", "&", $password);

            $txt = "username: " . $user . "\r\npasswd: \"" . $password . '"';
            $userFilename = str_ireplace('user', '', $user);
            $userFilename = str_ireplace('cis', '', $userFilename);
            $userFilename = str_ireplace('inv', '', $userFilename);
            $userFilename = str_ireplace('_', '-', $userFilename);
            $userFilename = str_ireplace(' ', '', $userFilename);
            if (strlen($userFilename) == 0) {
                $userFilename = 'cis';
            }
            $userFilename = strtolower($userFilename);
            $filename = substr($files[$i], 0, -4) . '-' . $userFilename;

            // ANSIBLE CREDENTIALS
            $putty .= $filename . ' ';

            $myfile = fopen($dirCredentials . '/' . ((strpos($files[$i], 'prd') == false) ? 'stg' : 'prd') . '/' . $filename, "w");
            fwrite($myfile, $txt);
            fclose($myfile);

            $myfileData = fopen($dirData . '/' . ((strpos($files[$i], 'prd') == false) ? 'stg' : 'prd') . '/' . $filename . '.xml', "w");
            fwrite($myfileData, "");
            fclose($myfileData);

            // ANSIBLE SCHEMA FILE
            $schemas .= '<br />#EMEA ' . $filename . '<br />&nbsp;&nbsp;- urls: https://augw' . ((strpos($files[$i], 'prd') == false) ? 'stg' : '') . '.svcs.entsvcs.com/' . (strtoupper(substr($files[$i], strpos($files[$i], '-', 6) + 1, -4))) . '/ExternalStreamReceive/StreamReceive.svc<br />&nbsp;&nbsp;&nbsp;&nbsp;fname: ./schema/emea/' . ((strpos($files[$i], 'prd') == false) ? 'stg' : 'prd') . '/' . $filename . '.xml<br />&nbsp;&nbsp;&nbsp;&nbsp;cred: ./credentials/emea/' . ((strpos($files[$i], 'prd') == false) ? 'stg' : 'prd') . '/' . $filename . '<br />';
        }
    }
    echo '<br /><h /><br />PUTTY: ' . $putty . '<br /><hr /><br />';
    echo '<br /><hr /><br />' . $schemas . '<br /><hr /><br />';

}

function sysToolsAwxFileNormalizer()
{

    echo '<script><!--
            $(function() {$( "#tabs" ).tabs();});
			$(function () {
                $("#go").click(function(){
                    $(document.body).css({\'cursor\' : \'wait\'});
					var attr = "?a=sysToolsAwxFileNormalizerGo";
					$.ajax({
							url: (attr),
							context: document.body
						}).done(function(data) {
                            $(document.body).css({\'cursor\' : \'default\'});
							$("#output").html(data)
						});
				});
			--></script>';

    sysPrintBlockHeader(10, 'AWS file normalizer');
    echo '   
            This will normalize file names for AWX.
            <br />
            <a id="go" class="btn btn-secondary">Go</a>
            <br />
            <br />
            <textarea id="output" class="form-control" rows="12" placeholder="Click Go ..."></textarea>
           ';

    sysPrintBlockFooter();
}

function sysToolsAwxFileNormalizerGo()
{
    $dir = './it-files-to-normalize';
    $files = scandir($dir);

    $normalizeArray = array(
        'Description',
        'BriefDescription',
        'SourceEntityId',
        'CallerID',
        'CaseLog',
        'ProblemType',
        'AssignmentGroup',
        'FileData',
        'Action',
        'ExtendedDescription',
        'LogicalCIName',
        'ASSYSTTicketID',
        'Title',
        'SolutionDescription',
        'SourceCaseId',
        'DestinationCaseId',
        'SourceParentCaseId',
        'DestinationParentCaseId',
        'TransactionId',
        'TransactionID',
        'BackoutPlan',
        'SourceTransactionId',
        'CisTransactionId',
        'DestinationEntityId',
        'ImplementationPlan',
        'RiskAssessmentComments',
        'SourceParentEntityId',
        'Question',
        'Answer',
        'PartNumber',
        'Name',
        'sender',
        'receiver',
        'msgType',
        'docId'
    );
    //
    $prefixesArr = array(
        'v3:',
        'v2:',
        'hp:',
        'ns0:',
        'ns1:',
        'ns2:',
        'ns3:',
        'ns62:',
        'p:',
        'u:',
        'hp1-ct:',
        ''
    );

    for ($i = 0; $i < count($files); $i++) {
        if ($i < 2)
            continue;
        $file = file_get_contents($dir . '/' . $files[$i]);
        $automatedString = substr($files[$i], 0, strpos($files[$i], "."));
        foreach ($normalizeArray as $normalizeArrayItem) {
            // Add prefixes!
            foreach ($prefixesArr as $prefixesArrItem) {
                $normalizeArrayItemPrefix = $prefixesArrItem . '' . $normalizeArrayItem;

                if (strpos($files[$i], ".xml") == false)
                    continue;
                // here to do bussiness
                $automatedStringVal = $automatedString . '-' . $normalizeArrayItemPrefix . '-{{001}}';

                // 1 search stert string and then end of that particular XML element
                // 2 search the next <
                // 3replace the data iwth AutomatedStringVal
                $positionOfelement = strpos($file, '<' . $normalizeArrayItemPrefix);
                if ($positionOfelement == false) {
                    continue;
                } else {
                    $positionOfelementEnd = strpos($file, ">", $positionOfelement) + 1;
                    $positionOfEndingTheValue = strpos($file, "<", $positionOfelementEnd);
                    // only if it was not empty before
                    if (strpos($file, '</' . $normalizeArrayItemPrefix . '>') == false) {
                        continue;
                    } else {
                        $file = substr($file, 0, $positionOfelementEnd) . $automatedStringVal . substr($file, $positionOfEndingTheValue);
                    }
                }
            }
        }

        $newFile = fopen($dir . '/' . $files[$i], "w");
        fwrite($newFile, $file);
        fclose($newFile);
    }

    echo '<br /><hr /><br />Done!<br /><hr /><br />';
}
