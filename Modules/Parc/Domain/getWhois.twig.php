<?php
$ip = $_GET['ip'];
$whois = shell_exec("whois ".$ip);
$data = explode("\n",$whois);
array_walk($data,function (&$a){
   $a = explode(':',$a);
});
$ret = '';
foreach($data as $line){
    if($line[0] == 'org-name'){
        $ret = $line[1];
        break;
    }
}
if (empty($ret)){
    foreach($data as $line){
        if($line[0] == 'role') {
            $ret = $line[1];
            break;
        }
    }
}
if (empty($ret)){
    foreach($data as $line){
        if($line[0] == 'mnt-by') {
            $ret = $line[1];
            break;
        }
    }
}

echo trim($ret);
