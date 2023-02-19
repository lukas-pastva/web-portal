<?php

function sysModuleGet($section)
{
    return sqlGetRow($sql = "SELECT * FROM `sysModule` where id = '$section'");
}

function sysJpgCompress()
{

    echo '<script><!--            
			$(function () {
				
                $("#go").click(function(){
                    $(document.body).css({\'cursor\' : \'wait\'});
					var attr = "?a=sysJpgCompressGo";
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

    sysPrintBlockHeader(10, 'AWS log beautifier');
    echo '
            This will resize images to 75% of their quality, recursively. Saving disk space.
            <br />
            <a id="go" class="btn btn-secondary">Go</a>
            <br />
            <br />
            <textarea id="output" class="form-control" rows="12" placeholder="Click Go ..."></textarea>
           ';

    sysPrintBlockFooter();
}

function sysJpgCompressGo($dir = './it-image-space-saver')
{
    $files = scandir($dir);

    for ($i = 0; $i < count($files); $i++) {
        if ($i < 2)
            continue;

        $file = $dir . '/' . $files[$i];
        if (is_dir($file)) {
            jpgImageSpaceSaver($file);
        } else if ((strpos($files[$i], ".jpg") != null) && filesize($file) > 500) {
            header('Content-Type: image/jpeg');
            $image = imagecreatefromjpeg($file);

            imagejpeg($image, $file, 60);
            imagedestroy($image);
        }
    }
    echo 'Dir resized: ' . $dir . ' <br />';
}

function sysToolsStringReplacer()
{
    echo '<script><!--
            $(function() {$( "#tabs" ).tabs();});
			$(function () {
                $("#go").click(function(){                    
					var attr = "?a=fileUploader";
                    var formData = new FormData();
					var files = $("#file")[0].files;
					
					if(files.length > 0 ){
						$(document.body).css({\'cursor\' : \'wait\'});
						formData.append(\'file\', files[0]);
						formData.append(\'directory\', \'it-string-replacer\');
						$.ajax({
							url: (attr),
							type: "POST",						  
							data : formData,	  
							processData: false,
							contentType: false,
							context: document.body					 
						}).done(function(data) {
							console.log(data);
							if(data=="ok"){
								$("#output").html($("#output").html()+"File uploaded. <br />Unzipping.<br />");
								var attr2 = "?a=zipExtractor";
								var formData2 = new FormData();
								formData2.append(\'unziping-dir\', \'it-string-replacer\');
								$.ajax({
									url: (attr2),
									type: "POST",						  
									data : formData2,	  
									processData: false,
									contentType: false,
									context: document.body					 
								}).done(function(data2) {
									console.log(data2);
									if(data2=="ok"){
										$("#output").html($("#output").html()+"Unzipping Done. <br />Performing mass str-replace.<br />");
                                        var attr3 = "?a=stringReplacer";
                                        var formData3 = new FormData();
                                        formData3.append(\'input-0\', $("#input-0").val());
                                        formData3.append(\'input-1\', $("#input-1").val());
                                        $.ajax({
                                            url: (attr3),
                                            type: "POST",						  
                                            data : formData3,	  
                                            processData: false,
                                            contentType: false,
                                            context: document.body					 
                                        }).done(function(data3) {
                                            console.log(data3);
                                            if(data3=="ok"){
                                                $("#output").html($("#output").html()+"Mass str-replace Done. <br />Zipping<br />");
                                                var attr4 = "?a=zipMaker";
                                                var formData4 = new FormData();
                                                formData4.append(\'zipped-dir\', \'it-string-replacer\');
                                                $.ajax({
                                                    url: (attr4),
                                                    type: "POST",						  
                                                    data : formData4,
                                                    processData: false,
                                                    contentType: false,
                                                    context: document.body					 
                                                }).done(function(data) {
                                                    $(document.body).css({\'cursor\' : \'default\'});
                                                    if(data=="ok"){
                                                        $("#output").html($("#output").html()+"Zipping Done. <br /><a href=\"./it-string-replacer/it-string-replacer.zip\">Download here.</a><br />");
                                                        var formData2 = new FormData();
                                                        formData2.append(\'input-0\', $("#input-0").val());
                                                        formData2.append(\'input-1\', $("#input-1").val());
                                                    }
                                                });
                                            }
                                        });
									}
								});
							}
						});
					}else{
						alert ("No file selected.");
					}
				});

			});
			--></script>';

    sysPrintBlockHeader(10, 'AWS log beautifier');

    echo 'Use this page if you need to replace string within complex file structures.<br /><br />
            1. Upload .ZIP with file structure. 
            <input type="file" id="file" name="file" /><br /><br />
            2. Source text:
            <textarea id="input-0" class="form-control" rows="6" placeholder="Enter ..."></textarea>
            3. Destination text:
            <textarea id="input-1" class="form-control" rows="6" placeholder="Enter ..."></textarea>            
            <br />
            <a id="go" class="btn btn-secondary">Go</a>            
           ';

    sysPrintBlockFooter();
}

function sysToolsStringReplacerGo($srcStr, $dstStr, $dir = './it-string-replacer', $firstCall = true)
{
    $files = scandir($dir);

    for ($i = 0; $i < count($files); $i++) {
        if ($i < 2)
            continue;

        $file = $dir . '/' . $files[$i];
        if (is_dir($file)) {
            stringReplacer($srcStr, $dstStr, $file, false);
        } else if (filesize($file) > 1) {

            $fileStr = file_get_contents($file);

            $fileStr = str_replace($srcStr, $dstStr, $fileStr);

            file_put_contents($file, $fileStr);
        }
    }
    if ($firstCall) {
        echo 'ok';
    }
}

function fileUploader($dir)
{
    if (cleanupDir($dir)) {
        if (isset($_FILES['file']['tmp_name'])) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dir . '/' . $dir . '.zip')) {
                echo 'ok';
            }
        }
    }
}

function zipExtractor($sourceDir, $sourceFile, $destinationDir)
{
    $zip = new ZipArchive;
    if ($zip->open($sourceDir . $sourceFile) === TRUE) {
        $zip->extractTo($destinationDir);
        $zip->close();
        unlink($sourceDir . $sourceFile);
    } else {
        echo 'cannot open file: ' . $sourceDir . $sourceFile;
    }
    echo 'ok';
}

function zipMaker($sourceDir, $destinationDir, $zipName)
{

    if (!is_dir($sourceDir)) return false;

    $zip = new ZipArchive();
    $zip->open($destinationDir . '/' . $zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourceDir));

            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();
}

function cleanupDir($dir, $firstCall = true)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? cleanupDir("$dir/$file", false) : unlink("$dir/$file");
    }
    if ($firstCall) {
        return true;
    } else {
        return rmdir($dir);
    }
}

function isModuleEncrypted($section, $attrs)
{
    $encrypted = false;
    foreach ($attrs as $attr) {
        if ($attr['type'] == 'openssl') {
            $encrypted = true;
            break;
        }
    }
    return $encrypted && (!array_key_exists('module-' . $section, $_SESSION) || !(strlen($_SESSION['module-' . $section]) > 0));
}

function sysAttributeIsHidden($attribute, $section, $attributes)
{
    $isHidden = false;
    if (
        ($attribute['db'] == 'daily' || $attribute['db'] == 'monthly' /*|| $key == 'id'*/)
        ||
        (($attribute['type'] == 'openssl') && (isModuleEncrypted($section, $attributes)))
    ) {
        $isHidden = true;
    }

    return $isHidden;
}