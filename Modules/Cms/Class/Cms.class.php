<?php

class Cms extends Module {

    /**
     * Chargement de templates
     */
    public function loadTemplate($t){
        //Recuperation de la Page
        $query = $GLOBALS['Systeme']->getRegVars('Query');
        $obj = explode('/', $query, 2);
        $page = Sys::getOneData($obj[0], $obj[1]);

        if(!$page->Display)
            header('Location: /');

        $Bloc=new Template();
        $Bloc->setConfig($page->TemplateConfig,'CmsDefault',$page->HtmlConfig);
        $Bloc->init();
        return $Bloc;
    }

}