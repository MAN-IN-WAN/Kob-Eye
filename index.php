<?php

define('ROOT_DIR', dirname(__FILE__).'/');

// Verif taille fichier log
if(filesize(ROOT_DIR.'Log/Systeme.log') > 1500000000) file_put_contents(ROOT_DIR.'Log/Systeme.log', '');

// Ajout automatique des WWW si non local
/*if (!preg_match("#^(.+)\.(.+)\.(.+)$#",$_SERVER["HTTP_HOST"],$Out) and $_SERVER["HTTP_HOST"] != 'localhost' and strpos($_SERVER["HTTP_HOST"], '.local') != strlen($_SERVER["HTTP_HOST"]) - 6){
	header('Status: 301 Moved Permanently', false, 301);
	header('Location: http://www.'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
	exit();
}*/
//Iteration du Chrono
include('Class/Root.class.php');
include('Class/Debug/Chrono.class.php');
$Chrono = new Chrono();
$GLOBALS["Chrono"]=$Chrono;
$Chrono->start();
$Chrono->start("CLASS LOAD");
include('Class/Systeme/Sys.class.php');
include('Class/Rpc/IWebservice.interface.php');
include('Class/Conf/Conf.class.php');
include('Class/Debug/Klog.class.php');
include('Class/Debug/Error.class.php');
include('Class/Template/Skin.class.php');
include('Class/Systeme/Module.class.php');
include('Class/Systeme/Connection.class.php');
include('Class/Systeme/Plugin.class.php');
include('Class/DataBase/DbAnalyzer.class.php');
include('Class/DataBase/ObjectClass.class.php');
include('Class/DataBase/Association.class.php');
include('Class/DataBase/ObjectConst.class.php');
include('Class/DataBase/genericClass.class.php');
include('Class/DataBase/View.class.php');
include('Class/DataBase/Drivers/mysqlDriver.class.php');
include('Class/DataBase/Drivers/sqlFunctions.class.php');
include('Class/DataBase/Drivers/sqlCheck.class.php');
include('Class/DataBase/Drivers/sqliteDriver.class.php');
include('Class/DataBase/Drivers/sqlInherit.class.php');
include('Class/DataBase/Drivers/textDriver.class.php');
include('Class/DataBase/Drivers/fileDriver.class.php');
include('Class/DataBase/Drivers/Flatfile.class.php');
include('Class/Beacon/Beacon.class.php');
include('Class/Beacon/Bloc.class.php');
include('Class/Beacon/Condition.class.php');
include('Class/Beacon/Info.class.php');
include('Class/Beacon/Lib.class.php');
include('Class/Beacon/Stats.class.php');
include('Class/Beacon/Storproc.class.php');
include('Class/Beacon/charUtils.class.php');
include('Class/Beacon/editStruct.class.php');
include('Class/Process/Process.class.php');
include('Class/Process/Parser.class.php');
include('Class/Process/Trigger.class.php');
include('Class/Process/Trigger/TriggerFunction.class.php');
include('Class/Process/Trigger/Classement.class.php');
include('Class/Process/Trigger/Journal.class.php');
include('Class/Process/Trigger/Total.class.php');
include('Class/Utils/Utils.class.php');
include('Class/Lib/xml2array.class.php');
include('Class/Utils/Session.class.php');
include('Class/Utils/JsonP.class.php');
include('Class/More.php');
$Chrono->stop("CLASS LOAD");

function __autoload($className) {
	$folder=Root::classFolder($className);
	//$GLOBALS["Chrono"]->start("Lazy load ".$className);
	if($folder) require_once($folder.'/'.$className.'.class.php');
	//$GLOBALS["Chrono"]->stop("Lazy load ".$className);
}
//Gestion des requetes d'autorisations. OPTIONS
if($_SERVER['REQUEST_METHOD'] == "OPTIONS"){
	header("Content-type: text/json; charset=UTF-8");
	header("Accept-Ranges:bytes");
	header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
	header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE");
	header("Access-Control-Allow-Origin: *");
	exit(0);
}else{
	//Definition du lien par defaut
	$Systeme = new Sys($_SERVER["REQUEST_URI"],$_SERVER["HTTP_HOST"]);
    $GLOBALS["Chrono"]->start("TOTAL CONNEXION");
	$Systeme->Connect();
    $GLOBALS["Chrono"]->stop("TOTAL CONNEXION");
    $GLOBALS["Chrono"]->start("TOTAL AFFICH");
    $Systeme->Affich();
    $GLOBALS["Chrono"]->stop("TOTAL AFFICH");
    $Systeme->Log->log($Chrono->total());
	$Systeme->Close();
	$Chrono->stop();
}
