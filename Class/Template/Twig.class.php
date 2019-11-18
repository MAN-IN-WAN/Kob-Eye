<?php
require 'Class/Twig/Autoloader.php';
Twig_Autoloader::register();
class KeTwig{
    static $Twig;
    static $Loader;
    static $templates = array();
    static $renderedTemplates = array();
    public static function initTwig() {
        KeTwig::$Loader = new Twig_Loader_Filesystem('.');
        if (TWIG_CACHE) {
            $config = array(
                'cache' => 'Data/Twig',
                'debug' => true
            );
        }else{
            $config=array();
        }
        KeTwig::$Twig = new Twig_Environment(KeTwig::$Loader, $config);
        KeTwig::$Twig->addExtension(new Twig_Extension_StringLoader());
        KeTwig::$Twig->addExtension(new Twig_Extension_Debug());
        KeTwig::$Twig->addFunction(new Twig_SimpleFunction('module','KeTwig::callModule'));
        KeTwig::$Twig->addFunction(new Twig_SimpleFunction('component','KeTwig::callComponent'));
    }
    public static function loadTemplate($template) {
        $t = explode('/',$template);
        $file = array_pop($t);
        $dir = implode('/',$t);
        //KeTwig::$Loader->addPath($dir);
/*        if ($template == 'Skins/Css34/Modules/Reservation/Spectacle/List.twig'){
            KeTwig::$templates[$template] = KeTwig::$Twig->loadTemplate($dir.'/'.$file);
            echo KeTwig::$templates[$template]->render(array());
            die();
        }*/
        return KeTwig::$templates[$template] = KeTwig::$Twig->loadTemplate($dir.'/'.$file);
    }
    public static function render($template, $vars){
        //echo "zob => $template \r\n";
        if (file_exists($template.'.php'))
            include($template.'.php');
        $tempname = md5($template.microtime());
        /*foreach (debug_backtrace(0) as $z){
            unset($z['args']);
            print_r($z);
        }*/
        /*if ($template == 'Skins/Css34/Modules/Reservation/Spectacle/ListBundle.twig'){
            //unset($vars['spectacle']);
            echo KeTwig::$templates[$template]->render($vars);
            die();
        }*/
        //return KeTwig::$templates[$template]->render($vars);
        KeTwig::$renderedTemplates[$tempname] = KeTwig::$templates[$template]->render($vars);
        return '##TWIG##'.$tempname.'##TWIG##';
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

    /**
     * processTemplates
     * @param $content
     *
     */
    public static function processTemplates($content) {
        $out = $content;
        foreach (array_keys(KeTwig::$renderedTemplates) as $t) {
            $out = str_replace('##TWIG##'.$t.'##TWIG##',KeTwig::$renderedTemplates[$t],$out);
        }
        if ($out == $content)return $out;
        else return KeTwig::processTemplates($out);
    }
}
