<?php
class ApcCache {

    static $iTtl = 600; // Time To Live
    static $bEnabled = false; // APC enabled?
    static $tempStore = array();

    // constructor
    static function init() {
        ApcCache::$bEnabled = false;//extension_loaded('apc');
    }

    // get data from memory
    static function getData($sKey) {
        $bRes = false;
        if (ApcCache::$bEnabled) {
            $vData = apc_fetch($sKey, $bRes);
            return ($bRes) ? $vData : null;
        }else{
            if (isset(ApcCache::$tempStore[$sKey])) {
                return ApcCache::$tempStore[$sKey];
            }else return false;
        }
    }

    // save data to memory
    static function setData($sKey, $vData) {
        if (ApcCache::$bEnabled) {
            return apc_store($sKey, $vData, ApcCache::$iTtl);
        }else{
            ApcCache::$tempStore[$sKey] = $vData;
            return true;
        }
    }

    // delete data from memory
    static function delData($sKey) {
        if (ApcCache::$bEnabled)
            return (apc_exists($sKey)) ? apc_delete($sKey) : true;
        else {
            unset(ApcCache::$tempStore[$sKey]);
            return false;
        }
    }

    static function clearData() {
        if (ApcCache::$bEnabled) {
            apc_clear_cache();
        }
        ApcCache::$tempStore = array();
    }
}

//initialisation
ApcCache::init();
?>
