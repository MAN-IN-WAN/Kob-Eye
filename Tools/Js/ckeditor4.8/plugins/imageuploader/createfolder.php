<?php

// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

session_start();

if(!isset($_SESSION['username'])) {
    exit;
}

// checking lang value
if(isset($_COOKIE['sy_lang'])) {
    $load_lang_code = $_COOKIE['sy_lang'];
} else {
    $load_lang_code = "en";
}

// including lang files
switch ($load_lang_code) {
    case "en":
        require(__DIR__ . '/lang/en.php');
        break;
    case "pl":
        require(__DIR__ . '/lang/pl.php');
        break;
}



// Including the plugin config file, don't delete the following row!
require(__DIR__ . '/pluginconfig.php');

$path = $usersiteroot.$_POST['foldernew'];

$preExamplePath = "$useruploadpath/test.txt";
$tmpUserUPath = pathinfo($preExamplePath);
$useruploadpathDirname = $tmpUserUPath['dirname'];
if(strpos($path , $useruploadpathDirname) === 0){
    if(!is_dir($path)){
        $path = str_replace($useruploadpath,'',$path);
        $split = explode('/',$path);
        $dir = $useruploadpath;

        foreach($split as $f){
            if(!is_dir($dir.'/'.$f))
                mkdir($dir.'/'.$f,0755);
            $dir = $dir.'/'.$f;
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);

    } else{
        echo '
        <script>
        swal({
          title: "Impossible de créer le dossier",
          text: "Ce dossier existe déjà.",
          type: "error",
          closeOnConfirm: false
        },
        function(){
          history.back();
        });
        </script>
    ';
    }

} else{
    echo '
        <script>
        swal({
          title: "Impossible de créer le dossier",
          text: "Vous n\'avez pas l\'autorisation de créer un dossier à cet endroit.",
          type: "error",
          closeOnConfirm: false
        },
        function(){
          history.back();
        });
        </script>
    ';
}

