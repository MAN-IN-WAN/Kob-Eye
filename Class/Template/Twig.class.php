<?php
require 'Class/Twig/Autoloader.php';
Twig_Autoloader::register();
class KeTwig{
    static $Twig;
    static $Loader;
    static $templates = array();
    public static function initTwig() {
        KeTwig::$Loader = new Twig_Loader_Filesystem('.');
        KeTwig::$Twig = new Twig_Environment(KeTwig::$Loader, array(
            /*'cache' => 'Data/Twig'*/
        ));
        KeTwig::$Twig->addExtension(new Twig_Extension_StringLoader());
        KeTwig::$Twig->addFunction(new Twig_SimpleFunction('module','KeTwig::callModule'));
        KeTwig::$Twig->addFunction(new Twig_SimpleFunction('component','KeTwig::callComponent'));
    }
    public static function loadTemplate($template) {
        $t = explode('/',$template);
        $file = array_pop($t);
        $dir = implode('/',$t);
        //KeTwig::$Loader->addPath($dir);
        return KeTwig::$templates[$template] = KeTwig::$Twig->loadTemplate($dir.'/'.$file);
    }
    public static function render($template, $vars){
        //echo "zob => $template \r\n";
        if (file_exists($template.'.php'))
            include($template.'.php');
        /*foreach (debug_backtrace(0) as $z){
            unset($z['args']);
            print_r($z);
        }*/
        return KeTwig::$templates[$template]->render($vars);
    }
    public static function callModule($var) {
        $bl = new Bloc();
        $bl->setFromVar($var,'',array('BEACON'=>'MODULE'));
        $bl->init();
        $bl->Generate();
        return $bl->Affich();
    }
    public static function callComponent($var) {
        $bl = new Component();
        $bl->setFromVar($var,'',array('BEACON'=>'COMPONENT'));
        $bl->init();
        $bl->Generate();
        return $bl->Affich();
    }

}