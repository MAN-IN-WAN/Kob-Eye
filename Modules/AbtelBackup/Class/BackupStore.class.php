<?php
class BackupStore extends genericClass{


    public static function getDiskUsage(){
        $stores = Sys::getData('AbtelBackup','BackupStore');

        foreach($stores as $store){
            if($store->Type == 'Local'){
                $store->Size = AbtelBackup::localExec('df -BM --output=size /backup | tail -n 1'); //pour passe en ko virer -BG
                $store->NfsSize = AbtelBackup::getSize('/backup/nfs');
                $store->BorgSize = AbtelBackup::getSize('/backup/borg');
                $store->Save();
            } else {
                //TODO : Remote
            }
        }
    }
}