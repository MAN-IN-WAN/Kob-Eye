<?php

class Cms extends Module {

    /**
     * Chargement de templates
     */
    public function loadTemplate($t){
        //Recuperation de la Page
        $menu = Sys::$CurrentMenu;
        $page = $menu->getOneChild('Page');

        $Bloc=new Template();
        $Bloc->setConfig($page->TemplateConfig,'CmsDefault',$page->HtmlConfig);
        $Bloc->init();
        return $Bloc;
    }

}