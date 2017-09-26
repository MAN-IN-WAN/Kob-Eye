<?php
/**
 * Class Parc
 * Installation des taches plafnifiées
 * 0 6 * * * apache /usr/bin/php /var/www/html/cron.php parc.azko.fr Parc/Bash/Renew.cron
 * *\/5 * * * * apache /usr/bin/php /var/www/html/cron.php parc.azko.fr Parc/Bash/Execute.cron
 */
class Parc extends Module{
    public $classLoader=null;
    /**
	 * Surcharge de la fonction init
	 * Avant l'authentification de l'utilisateur
	 * @void 
	 */
	function init (){
		parent::init();

        require_once 'Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
        @include_once 'Class/Lib/SimpleXmlDebug/simplexml_dump.php';
        @include_once 'Class/Lib/SimpleXmlDebug/simplexml_tree.php';

        $this->classLoader = new SplClassLoader('Zimbra', realpath('Class/Lib/')); // Point this to the src folder of the zcs-php repo
        $this->classLoader->register();
	}
	/**
	 * Surcharge de la fonction postInit
	 * Après l'authentification de l'utilisateur
	 * Toutes les fonctionnalités sont disponibles
	 * @void 
	 */
	function postInit (){
		parent::postInit();
		//chargement des variables globales par défaut pour le module boutique
		$this->initGlobalVars();
	}
        /**
         * Surcharge de la fonction Check
         * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
         * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
         */
        function Check () {
           parent::Check();
           //teste le role
           $r = Sys::getData('Systeme','Role/Title=PARC_CLIENT');
           if (sizeof($r)){
               $r = $r[0]; 
               //teste le groupe
               $g = $r->getChildren('Group');
               if (sizeof($g)){
                   //tout est ok 
               }else{
                    $this->createGroup($r);
               }
           }else{
                //il faut tout créer
                //création du role
                $r = genericClass::createInstance('Systeme','Role');
                $r->Title = "PARC_CLIENT";
                $r->Save();
                //création du groupe
                $this->createGroup($r);
           }
            //teste le role revendeur
            $r = Sys::getData('Systeme','Role/Title=PARC_REVENDEUR');
            if (sizeof($r)){
                $r = $r[0];
                //teste le groupe
                $g = $r->getChildren('Group');
                if (sizeof($g)){
                    //tout est ok
                }else{
                    $this->createGroupRevendeur($r);
                }
            }else{
                //il faut tout créer
                //création du role
                $r = genericClass::createInstance('Systeme','Role');
                $r->Title = "PARC_REVENDEUR";
                $r->Save();
                //création du groupe
                $this->createGroupRevendeur($r);
            }
            //teste l'accès revendeur principal
            $rev = Sys::getCount('Parc','Revendeur');
            if (!$rev){
                //creation du revendeur principal
                $re = genericClass::createInstance('Parc','Revendeur');
                $re->Set('Nom','Revendeur');
                $re->Set('AccesUser','revendeur');
                $re->Set('AccesPass','revendeur');
                $re->Set('AccesActif',true);
                $re->Set('Email',$this->AccesUser.'@'.$_SERVER["SERVER_NAME"]);
                $re->Save();
            }
            //teste le role technicien
            $r = Sys::getData('Systeme','Role/Title=PARC_TECHNICIEN');
            if (sizeof($r)){
                $r = $r[0];
                //teste le groupe
                $g = $r->getChildren('Group');
                if (sizeof($g)){
                    //tout est ok
                }else{
                    $this->createGroupTechnicien($r);
                }
            }else{
                //il faut tout créer
                //création du role
                $r = genericClass::createInstance('Systeme','Role');
                $r->Title = "PARC_TECHNICIEN";
                $r->Save();
                //création du groupe
                $this->createGroupTechnicien($r);
            }
            //teste l'existence d'une template
            $tps = Sys::getCount('Parc','DomainTemplate');
            if (!$tps){
                //creation d'une template d'exemple
                $re = genericClass::createInstance('Parc','DomainTemplate');
                $re->Set('Nom','Template par défaut en exemple');
                $re->Set('Contenu','<TEMPLATE>
                <SOUS_DOMAINE>
                        <CN>A:</CN>
                        <IP>5.196.207.219</IP>
                        <TYPE>Subdomain</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>A:mail</CN>
                        <IP>178.32.130.27</IP>
                        <TYPE>Subdomain</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>CNAME:imap</CN>
                        <DNSDOMAINNAME>imap</DNSDOMAINNAME>
                        <DNSCNAME>mail</DNSCNAME>
                        <TYPE>CNAME</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>CNAME:pop</CN>
                        <DNSDOMAINNAME>pop</DNSDOMAINNAME>
                        <DNSCNAME>mail</DNSCNAME>
                        <TYPE>CNAME</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>CNAME:pop3</CN>
                        <DNSDOMAINNAME>pop3</DNSDOMAINNAME>
                        <DNSCNAME>mail</DNSCNAME>
                        <TYPE>CNAME</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>CNAME:smtp</CN>
                        <DNSDOMAINNAME>smtp</DNSDOMAINNAME>
                        <DNSCNAME>mail</DNSCNAME>
                        <TYPE>CNAME</TYPE>
                </SOUS_DOMAINE>
                <SOUS_DOMAINE>
                        <CN>CNAME:www</CN>
                        <DNSDOMAINNAME>www</DNSDOMAINNAME>
                        <DNSCNAME></DNSCNAME>
                        <TYPE>CNAME</TYPE>
                </SOUS_DOMAINE>
                <NAME_SERVER>
                        <DNSCNAME>ns1.azko.fr</DNSCNAME>
                        <CN>NS:1</CN>
                        <TYPE>NS</TYPE>
                </NAME_SERVER>
                <NAME_SERVER>
                        <DNSCNAME>ns2.azko.fr</DNSCNAME>
                        <CN>NS:2</CN>
                        <TYPE>NS</TYPE>
                </NAME_SERVER>
                <MAIL_SERVER>
                        <DNSCNAME>mail</DNSCNAME>
                        <CN>MX:1</CN>
                        <TYPE>MX</TYPE>
                </MAIL_SERVER>
</TEMPLATE>');
                $re->Save();
            }

            //modification des serveurs de noms.
            /*$ns = Sys::getData('Parc','NS',0,10000);
            foreach ($ns as $n){
                if (empty($n->Dnscname)||1){
                    $nserv = Sys::getOneData('Parc','Server/NS/'.$n->Id);
                    $n->Dnscname = $nserv->DNSNom.'.';
                    $n->Save();
                }
                if (empty($n->Dnsdomainname)||1){
                    $ndom = Sys::getOneData('Parc','Domain/NS/'.$n->Id);
                    $n->Dnsdomainname = $ndom->Url.'.';
                    $n->Save();
                }
            }*/
        }
        /**
         * Creation du groupe et de tout ses menus
         */
        private function createGroup($role){
            //creation du groupe 
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[PARC] Accès clients";
            $g->Skin = "ParcClient";
            $g->AddParent($role);
            $g->Save();
            //création des menus
            $m = genericClass::createInstance('Systeme','Menu');
            $m->Titre = "Tableau de bord";
            $m->Alias = "Systeme/User/DashBoard";
            $m->AddParent($g);
            $m->Save();
        }
        private function createGroupRevendeur($role){
            //creation du groupe revendeur
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[PARC] Accès revendeur";
            $g->Skin = "ParcClient";
            $g->AddParent($role);
            $g->Save();
            //création des menus
            $g->importMenus('YTo3OntpOjA7YToyNjp7czozOiJVcmwiO3M6MDoiIjtzOjU6IlRpdHJlIjtzOjE1OiJUYWJsZWF1IGRlIGJvcmQiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MjQ6IlBhcmMvUmV2ZW5kZXVyL0Rhc2hCb2FyZCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToxO2E6MjY6e3M6MzoiVXJsIjtzOjc6IkNsaWVudHMiO3M6NToiVGl0cmUiO3M6MTk6Ikdlc3Rpb24gZGVzIGNsaWVudHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvQ2xpZW50IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToyO2E6MjY6e3M6MzoiVXJsIjtzOjg6IkRvbWFpbmVzIjtzOjU6IlRpdHJlIjtzOjIwOiJHZXN0aW9uIGRlcyBkb21haW5lcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9Eb21haW4iO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMzAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjE6IjAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjM7YToyNjp7czozOiJVcmwiO3M6MTI6IkhlYmVyZ2VtZW50cyI7czo1OiJUaXRyZSI7czoyNToiR2VzdGlvbiBkZXMgaMOpYmVyZ2VtZW50cyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czo5OiJQYXJjL0hvc3QiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiNDAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjE6IjAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjQ7YToyNjp7czozOiJVcmwiO3M6ODoiVGVtcGxhdGUiO3M6NToiVGl0cmUiO3M6MjE6Ikdlc3Rpb24gZGVzIHRlbXBsYXRlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxOToiUGFyYy9Eb21haW5UZW1wbGF0ZSI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiI1MCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6NTthOjI2OntzOjM6IlVybCI7czo4OiJTZXJ2ZXVycyI7czo1OiJUaXRyZSI7czoyMDoiR2VzdGlvbiBkZXMgc2VydmV1cnMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvU2VydmVyIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjYwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aTo2O2E6MjY6e3M6MzoiVXJsIjtzOjY6IlRhY2hlcyI7czo1OiJUaXRyZSI7czoxODoiR2VzdGlvbiBkZXMgdGFjaGVzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjEwOiJQYXJjL1RhY2hlIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjcwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319fQ==');
        }
        private function createGroupTechnicien($role){
            //creation du groupe revendeur
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[PARC] Accès technicien";
            $g->Skin = "AngularAdmin";
            $g->AddParent($role);
            $g->Save();
            //création des menus
            $g->importMenus('YTo1OntpOjA7YToyODp7czozOiJVcmwiO3M6MTM6IlRhYmxlYXVEZUJvcmQiO3M6NToiVGl0cmUiO3M6MTU6IlRhYmxlYXUgZGUgYm9yZCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNDoiUGFyYy9EYXNoYm9hcmQiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MToiMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjE6IjAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyODp7czozOiJVcmwiO3M6MTQ6IkluZnJhc3RydWN0dXJlIjtzOjU6IlRpdHJlIjtzOjE0OiJJbmZyYXN0cnVjdHVyZSI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxOToiUGFyYy9JbmZyYXN0cnVjdHVyZSI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIxIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTozOntpOjA7YToyODp7czozOiJVcmwiO3M6NzoiQ2xpZW50cyI7czo1OiJUaXRyZSI7czo3OiJDbGllbnRzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjExOiJQYXJjL0NsaWVudCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiOTciO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyODp7czozOiJVcmwiO3M6MTM6IlBlcmlwaGVyaXF1ZXMiO3M6NToiVGl0cmUiO3M6MTU6IlDDqXJpcGjDqXJpcXVlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9EZXZpY2UiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MToiNSI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjI6Ijk3IjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToyO2E6Mjg6e3M6MzoiVXJsIjtzOjc6IlRpY2tldHMiO3M6NToiVGl0cmUiO3M6NzoiVGlja2V0cyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9UaWNrZXQiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMTAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoyOiI5NyI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX19aToyO2E6Mjg6e3M6MzoiVXJsIjtzOjU6IkNsb3VkIjtzOjU6IlRpdHJlIjtzOjE0OiJQcm9kdWl0cyBjbG91ZCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czo4OiJQYXJjL1dlYiI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiI1IjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTo0OntpOjA7YToyODp7czozOiJVcmwiO3M6NzoiU2VydmVycyI7czo1OiJUaXRyZSI7czo4OiJTZXJ2ZXVycyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9TZXJ2ZXIiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MToiMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwMSI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MTthOjI4OntzOjM6IlVybCI7czo4OiJEb21haW5lcyI7czo1OiJUaXRyZSI7czo4OiJEb21haW5lcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9Eb21haW4iO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MToiMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwMSI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MjthOjI4OntzOjM6IlVybCI7czo1OiJFbWFpbCI7czo1OiJUaXRyZSI7czo2OiJFbWFpbHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTU6IlBhcmMvQ29tcHRlTWFpbCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIxMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwMSI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MzthOjI4OntzOjM6IlVybCI7czoxMToiSGViZXJnZW1lbnQiO3M6NToiVGl0cmUiO3M6MTM6IkjDqWJlcmdlbWVudHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6OToiUGFyYy9Ib3N0IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MzoiMTAxIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319fX1pOjM7YToyODp7czozOiJVcmwiO3M6OToiRG9jdW1lbnRzIjtzOjU6IlRpdHJlIjtzOjk6IkRvY3VtZW50cyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNDoiUGFyYy9Eb2N1bWVudHMiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMTAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjM6e2k6MDthOjI4OntzOjM6IlVybCI7czo4OiJGYWN0dXJlcyI7czo1OiJUaXRyZSI7czo4OiJGYWN0dXJlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMjoiUGFyYy9GYWN0dXJlIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czozOiIxMDYiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyODp7czozOiJVcmwiO3M6OToiQ29tbWFuZGVzIjtzOjU6IlRpdHJlIjtzOjk6IkNvbW1hbmRlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNDoiUGFyYy9Db21tYW5kZXMiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czozOiIxMDYiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjI7YToyODp7czozOiJVcmwiO3M6ODoiQ29udHJhdHMiO3M6NToiVGl0cmUiO3M6ODoiQ29udHJhdHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTM6IkFidGVsL0NvbnRyYXQiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMzAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czozOiIxMDYiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX19fWk6NDthOjI4OntzOjM6IlVybCI7czo3OiJQcm9maWxlIjtzOjU6IlRpdHJlIjtzOjY6IlByb2ZpbCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMjoiUGFyYy9Qcm9maWxlIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjUwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjAiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX0=');
        }
	/**
	 * Initilisation des variables globales disponibles pour la boutique
	 */
	private function initGlobalVars(){
        if (!Sys::$User->Public){
            //initialisation client si connecté
            $Cls = Sys::$User->getChildren('Client');
            if (sizeof($Cls)){
                $this->_ParcClient = $Cls[0];
                $GLOBALS["Systeme"]->registerVar("ParcClient",$this->_ParcClient);
            }else{
                //test si revendeur
                $Rvs = Sys::$User->getChildren('Revendeur');
                if (sizeof($Rvs)){
                    $this->_ParcClient = $Rvs[0];
                    $GLOBALS["Systeme"]->registerVar("ParcClient",$this->_ParcClient);
                }
            }
        }
	}

	/**
     * Renouvellement des certificats
     */
	public function renewCertificates () {
        echo "renew certificate\r\n";
	    //recherche des hébergements à renouveller avec une expiration dans les prochains 30 jours
        $aps = Sys::getData('Parc','Apache/Ssl=1&&SslMethod=Letsencrypt');
        //pour chaque apache on crée une tache pour renouveller le certificat
        foreach ($aps as $a){
            if ($a->enableSsl()) echo "--> renew $a->ApacheServerName \r\n";
        }
    }
}
