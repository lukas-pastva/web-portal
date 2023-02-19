<?php

function sysApiCall($apiKey, $sectionId){
    $continue = false;
    $pages = sysModuleGetRows(true, 'webPage');

    foreach($pages as $page){
        if( ($page['apiKey']==$apiKey) && ( strpos(strtolower($sectionId), strtolower($page['name']))>0 ) ){
            $continue = true;
            break;
        }
    }
    if(!$continue){
        echo 'not allowed';
        return;
    }

    $section = sysSectionGet($sectionId);
    $module = sysModuleGet($section['id']);
    $attributes = sysModuleAttributesGet($module['id']);
    $abstract = $section['type'] == 'abstract';

    $rows = sysModuleGetRows($abstract, $section['id']);

    if(array_key_exists('timestamp', $attributes)){
        $rows = sysModuleOrderBy($rows, 'timestamp', false);
    }
    if(array_key_exists('timestampstart', $attributes)){
        $rows = sysModuleOrderBy($rows, 'timestampstart', false);
    }
    if(array_key_exists('order', $attributes)){
        $rows = sysModuleOrderBy($rows, 'order', false);
    }
    $rows = abstractDecode($rows, $attributes, $section);

    echo json_encode($rows);
}
