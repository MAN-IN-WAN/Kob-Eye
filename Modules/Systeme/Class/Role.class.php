<?php class Role extends genericClass{
    /**
     * hasRole
     * check role attributes
     * @param $roles array()
     * @param $attr
     * @return bool
     */
    static function hasRole($roles) {
        $roles = explode(',',$roles);
        $USR_ROLES = Sys::$User->getRoles();

        foreach ($roles as $r) {
            if (in_array(trim($r), $USR_ROLES)) {
                return true;
            }
        }
        return false;
    }

}