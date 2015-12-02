<?php
class Sesame extends Module {
    static function Ouverture () {
        exec('/usr/local/bin/ouverture > /dev/null 2>/dev/null &');
    }
}