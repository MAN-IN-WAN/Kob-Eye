<?php

$file = $_GET['path'];

if (file_exists($file)) {
    if(is_dir($file)){

        $main = basename($file);
//        $rootPath = realpath($file);
//        $zip = new ZipArchive();
//        $zip->open($main.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
//
//        // Create recursive directory iterator
//        /** @var SplFileInfo[] $files */
//        $files = new RecursiveIteratorIterator(
//            new RecursiveDirectoryIterator($rootPath,FilesystemIterator::FOLLOW_SYMLINKS),
//            RecursiveIteratorIterator::LEAVES_ONLY
//        );
//
//        foreach ($files as $name => $file)
//        {
//
//            // Skip directories (they would be added automatically)
//            if (!$file->isDir())
//            {
//                // Get real and relative path for current file
//                $filePath = $file->getRealPath();
//                $relativePath = substr($file, strlen($rootPath) + 1);
//                // Add current file to archive
//                $zip->addFile($filePath, $relativePath);
//            }
//        }
//
//        // Zip archive will be created only after closing object
//        $zip->close();
        try {
            AbtelBackup::localExec('cd "' . $file . '" && cd .. && zip -ry "/var/www/html/' . $main . '.zip" "' . $file.'"');
        } catch( Exception $e){
            print_r($e);
            die( 'Burp');
        }

        $file= './'.$main.'.zip';
    }



    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}