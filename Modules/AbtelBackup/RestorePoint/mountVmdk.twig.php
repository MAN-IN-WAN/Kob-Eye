<?php



/*if($vars['funcTempVars']['step'] == 1 ){
    $ffs = array();
    $base = '/backup/mount/vmdk';

    $files = scandir($base);
    foreach ($files as $file){
        if(strpos($file,'.') === 0) continue;

        $ffs[] = array(
            'name' => $file,
            'size' => filesize($base.'/'.$file),
            'folder' => is_dir($base.'/'.$file)
        );
    }
}*/