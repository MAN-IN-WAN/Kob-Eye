<?php
/*****************************************************************************
* KOB-EYE CRON ACCESS
* USAGE: /usr/bin/php $KOB-EYE_INSTALL_DIR/cron.php [DOMAIN] [URI] [STANDARD OUTPUT]
* EXAMPLE: $ php cron.php test.kob-eye.com /Systeme/test.htm false
*****************************************************************************/
define('ROOT_DIR', dirname(__FILE__).'/');
define('SQL_LITE_CACHE',0);
$_GET['mode'] = 'temoa';
$_GET['corpus'] = 'all';
$_GET['word'] = 'calli';


//importation de la class BashColors
include ('Class/Utils/BashColors.class.php');
$colors = new BashColors();

echo $colors->getColoredString("  _  __     _                            ", "green") . "\n";
echo $colors->getColoredString(" | |/ /___ | |__         ___ _   _  ___  ", "green") . "\n";
echo $colors->getColoredString(" | ' // _ \| '_ \ _____ / _ \ | | |/ _ \ ", "green") . "\n";
echo $colors->getColoredString(" | . \ (_) | |_) |_____|  __/ |_| |  __/ ", "green") . "\n";
echo $colors->getColoredString(" |_|\_\___/|_.__/       \___|\__, |\___| ", "green") . "\n";
echo $colors->getColoredString("                             |___/       ", "green") . "\n";
//La belle entete
/*echo $colors->getColoredString(" ___  __    ________  ________                 _______       ___    ___ _______       ", "green") . "\n";
echo $colors->getColoredString("|\  \|\  \ |\   __  \|\   __  \               |\  ___ \     |\  \  /  /|\  ___ \      ", "green") . "\n";
echo $colors->getColoredString("\ \  \/  /|\ \  \|\  \ \  \|\ /_  ____________\ \   __/|    \ \  \/  / | \   __/|     ", "green") . "\n";
echo $colors->getColoredString(" \ \   ___  \ \  \\\  \ \   __  \|\____________\ \  \_|/__   \ \    / / \ \  \_|/__   ", "green") . "\n";
echo $colors->getColoredString("  \ \  \\ \  \ \  \\\  \ \  \|\  \|____________|\ \  \_|\ \   \/  /  /   \ \  \_|\ \  ", "green") . "\n";
echo $colors->getColoredString("   \ \__\\ \__\ \_______\ \_______\              \ \_______\__/  / /      \ \_______\ ", "green") . "\n";
echo $colors->getColoredString("    \|__| \|__|\|_______|\|_______|               \|_______|\___/ /        \|_______| ", "green") . "\n";
echo $colors->getColoredString("                                                           \|___|/                    ", "green") . "\n";
*/



echo $colors->getColoredString("Loading classes ...", "green") . "\n";	
 
if (sizeof($argv)<3){
    echo $colors->getColoredString("ERROR: You need at least two parameters: [DOMAIN] [URI]", "red") . "\n";	
    echo $colors->getColoredString("ex: cron.php blahblah.com /Systeme/cron.htm", "yellow") . "\n";
    die();
}
// Verif taille fichier log
if(filesize(ROOT_DIR.'Log/Systeme.log') > 1500000000) file_put_contents('Log/Systeme.log', '');

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
include('Class/Template/Header.class.php');
include('Class/Systeme/Module.class.php');
include('Class/Systeme/Connection.class.php');
include('Class/Systeme/Plugin.class.php');
include('Class/Systeme/ApcCache.class.php');
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
include('Class/DataBase/Drivers/sqlInterval.class.php');
include('Class/DataBase/Drivers/textDriver.class.php');
include('Class/DataBase/Drivers/fileDriver.class.php');
include('Class/DataBase/Drivers/Flatfile.class.php');
include('Class/Beacon/Beacon.class.php');
include('Class/Beacon/Bash.class.php');
include('Class/Beacon/Bloc.class.php');
include('Class/Beacon/Condition.class.php');
include('Class/Beacon/Component.class.php');
include('Class/Beacon/Info.class.php');
include('Class/Beacon/Lib.class.php');
include('Class/Beacon/Stats.class.php');
include('Class/Beacon/Storproc.class.php');
include('Class/Beacon/charUtils.class.php');
include('Class/Beacon/editStruct.class.php');
include('Class/Beacon/Template.class.php');
include('Class/Beacon/Zone.class.php');
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
include('Class/Template/Twig.class.php');
$Chrono->stop("CLASS LOAD");

KeTwig::initTwig();

function __autoload($className) {
	$folder=Root::classFolder($className);
	$GLOBALS["Chrono"]->start("TOTAL CLASS LOAD");
	$GLOBALS["Chrono"]->start("load ".$className);
	if($folder) require_once($folder.'/'.$className.'.class.php');
	$GLOBALS["Chrono"]->stop("TOTAL CLASS LOAD");
	$GLOBALS["Chrono"]->stop("load ".$className);
}
//Declaration des Classes PHP
echo $colors->getColoredString("Framework initialisation ...", "green") . "\n";	

$GLOBALS['Systeme']=$Systeme;
//Definition du lien par defaut
$Systeme = new Sys($argv[2],$argv[1]);
echo $colors->getColoredString("Database connections ...", "green") . "\n";	
$Systeme->Connect();
//$Systeme->Log->log($Chrono->total());
echo $colors->getColoredString("Processing page ".$argv[2]."\r\n", "green") . "\n";
if (isset($argv[3])&& $argv[3]=="false")
    ob_start();
$Systeme->Affich();
if (isset($argv[3])&& $argv[3]=="false")
    ob_clean();
$Systeme->Close();
echo $colors->getColoredString("\nClosing connections ...", "green") . "\n";	
echo $colors->getColoredString("------------------------------------", "green") . "\n";	
$Chrono->stop();

