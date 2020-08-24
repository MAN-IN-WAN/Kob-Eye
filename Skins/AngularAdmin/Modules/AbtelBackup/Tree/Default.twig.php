<?php

$source = isset($_POST['path']) && $_POST['path'] != '-' ? $_POST['path'] : '/backup/mount/vmdk';

$children = array();

$files = scandir($source);
foreach($files as $file){
    if(strpos($file,'.') === 0) continue;
    $path = $source.'/'.$file;
    $children[] = array(
        "id" => md5($path),
        "text" => $file,
        "data" => $path,
        "children" => is_dir($path),
        'icon' => is_dir($path)?'jstree-folder':'jstree-file',
        'a_attr'=> array(
            "href"=> '/AbtelBackup/Download.htm?path='.$path
        )
    );
}
$cur = explode('/', $source);
$ret = array(
    "id"=> md5($source),
    "text"=> $source == '/backup/mount/vmdk' ? 'Partitions' : end($cur ),
    "data"=>$source,
    "children"=>$children,
    'icon' => 'jstree-folder',
    'a_attr'=> array(
        "href"=> '/AbtelBackup/Download.htm?path='.$source
    )
);

echo json_encode($ret);