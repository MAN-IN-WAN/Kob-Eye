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

            //teste le role contact
            $r = Sys::getData('Systeme','Role/Title=PARC_CONTACT');
            if (sizeof($r)){
                $r = $r[0];
                //teste le groupe
                $g = $r->getChildren('Group');
                if (sizeof($g)){
                    //tout est ok
                }else{
                    $this->createGroupContact($r);
                }
            }else{
                //il faut tout créer
                //création du role
                $r = genericClass::createInstance('Systeme','Role');
                $r->Title = "PARC_CONTACT";
                $r->Save();
                //création du groupe
                $this->createGroupContact($r);
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
            $g->importMenus('YTo0OntpOjA7YTozMTp7czozOiJVcmwiO3M6MTM6IlRhYmxlYXVEZUJvcmQiO3M6NToiVGl0cmUiO3M6MTU6IlRhYmxlYXUgZGUgYm9yZCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxODoiUGFyYy9EYXNoYm9hcmRQYXJjIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjI4OiJBY2PDqHMgVGVjaG5pY2llbiBNQU4gSU4gV0FOIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7TjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO047czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToxO2E6MzE6e3M6MzoiVXJsIjtzOjE0OiJJbmZyYXN0cnVjdHVyZSI7czo1OiJUaXRyZSI7czoxMzoiQ29uZmlndXJhdGlvbiI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxOToiUGFyYy9JbmZyYXN0cnVjdHVyZSI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIxIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6OToiUGFnZVRpdHJlIjtzOjA6IiI7czoxNToiUGFnZURlc2NyaXB0aW9uIjtzOjA6IiI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjEwOntpOjA7YTozMTp7czozOiJVcmwiO3M6NzoiU2VydmVycyI7czo1OiJUaXRyZSI7czo4OiJTZXJ2ZXVycyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9TZXJ2ZXIiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO047czoxNToiUGFnZURlc2NyaXB0aW9uIjtOO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YTozMTp7czozOiJVcmwiO3M6NzoiQ2xpZW50cyI7czo1OiJUaXRyZSI7czo3OiJDbGllbnRzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjExOiJQYXJjL0NsaWVudCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7TjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MToiMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7TjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO047czoxMDoiTWVudVBhcmVudCI7czoyOiIxMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MjthOjMxOntzOjM6IlVybCI7czo5OiJyZXZlbmRldXIiO3M6NToiVGl0cmUiO3M6OToiUmV2ZW5kZXVyIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjE0OiJQYXJjL1JldmVuZGV1ciI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiI1MCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7TjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO047czoxMDoiTWVudVBhcmVudCI7czoyOiIxMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MzthOjMxOntzOjM6IlVybCI7czo5OiJUZW1wbGF0ZXMiO3M6NToiVGl0cmUiO3M6MTk6Ik1vZMOobGVzIGRlIGRvbWFpbmUiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTk6IlBhcmMvRG9tYWluVGVtcGxhdGUiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjUwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6OToiUGFnZVRpdHJlIjtOO3M6MTU6IlBhZ2VEZXNjcmlwdGlvbiI7TjtzOjEwOiJNZW51UGFyZW50IjtzOjI6IjEwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aTo0O2E6MzE6e3M6MzoiVXJsIjtzOjU6IlRhc2tzIjtzOjU6IlRpdHJlIjtzOjE1OiJUYWNoZXMgbWFjaGluZXMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTA6IlBhcmMvVGFjaGUiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjYwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6OToiUGFnZVRpdHJlIjtOO3M6MTU6IlBhZ2VEZXNjcmlwdGlvbiI7TjtzOjEwOiJNZW51UGFyZW50IjtzOjI6IjEwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aTo1O2E6MzE6e3M6MzoiVXJsIjtzOjEwOiJQSFBWZXJzaW9uIjtzOjU6IlRpdHJlIjtzOjE1OiJWZXJzaW9ucyBkZSBQSFAiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTU6IlBhcmMvUEhQVmVyc2lvbiI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7TjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiNzAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO047czoxNToiUGFnZURlc2NyaXB0aW9uIjtOO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjY7YTozMTp7czozOiJVcmwiO3M6MTU6IlZlcnNpb25Mb2dpY2llbCI7czo1OiJUaXRyZSI7czoyMDoiVmVyc2lvbnMgYXBwbGljYXRpZnMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MjA6IlBhcmMvVmVyc2lvbkxvZ2ljaWVsIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjM6IjEwMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7czowOiIiO3M6MTU6IlBhZ2VEZXNjcmlwdGlvbiI7czowOiIiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjc7YTozMTp7czozOiJVcmwiO3M6OToiaW5jaWRlbnRzIjtzOjU6IlRpdHJlIjtzOjk6IkluY2lkZW50cyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMzoiUGFyYy9JbmNpZGVudCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czozOiIyMDAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO3M6MDoiIjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO3M6MDoiIjtzOjEwOiJNZW51UGFyZW50IjtzOjI6IjEwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aTo4O2E6MzE6e3M6MzoiVXJsIjtzOjg6IkFjdGl2aXR5IjtzOjU6IlRpdHJlIjtzOjg6IkFjdGl2aXR5IjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjEzOiJQYXJjL0FjdGl2aXR5IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjM6IjI1MCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7czowOiIiO3M6MTU6IlBhZ2VEZXNjcmlwdGlvbiI7czowOiIiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjk7YTozMTp7czozOiJVcmwiO3M6MTQ6IlNjaGVkdWxlZFRhc2tzIjtzOjU6IlRpdHJlIjtzOjE5OiJUw6JjaGVzIHBsYW5pZmnDqWVzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjIxOiJTeXN0ZW1lL1NjaGVkdWxlZFRhc2siO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MzoiMjYwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6OToiUGFnZVRpdHJlIjtzOjA6IiI7czoxNToiUGFnZURlc2NyaXB0aW9uIjtzOjA6IiI7czoxMDoiTWVudVBhcmVudCI7czoyOiIxMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX19aToyO2E6MzE6e3M6MzoiVXJsIjtzOjU6IkNsb3VkIjtzOjU6IlRpdHJlIjtzOjEwOiJQcm9kdWN0aW9uIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjg6IlBhcmMvV2ViIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtzOjA6IiI7czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjUiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO3M6MDoiIjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO3M6MDoiIjtzOjEwOiJNZW51UGFyZW50IjtzOjE6IjAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6NDp7aTowO2E6MzE6e3M6MzoiVXJsIjtzOjM6IkJkZCI7czo1OiJUaXRyZSI7czoxNzoiQmFzZXMgZGUgZG9ubsOpZXMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6ODoiUGFyYy9CZGQiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO047czoxNToiUGFnZURlc2NyaXB0aW9uIjtOO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTQiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YTozMTp7czozOiJVcmwiO3M6ODoiRG9tYWluZXMiO3M6NToiVGl0cmUiO3M6ODoiRG9tYWluZXMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvRG9tYWluIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6OToiUGFnZVRpdHJlIjtOO3M6MTU6IlBhZ2VEZXNjcmlwdGlvbiI7TjtzOjEwOiJNZW51UGFyZW50IjtzOjI6IjE0IjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToyO2E6MzE6e3M6MzoiVXJsIjtzOjk6Imluc3RhbmNlcyI7czo1OiJUaXRyZSI7czo5OiJJbnN0YW5jZXMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTM6IlBhcmMvSW5zdGFuY2UiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO3M6MDoiIjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMTAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO047czoxNToiUGFnZURlc2NyaXB0aW9uIjtOO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTQiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjM7YTozMTp7czozOiJVcmwiO3M6MTE6IkhlYmVyZ2VtZW50IjtzOjU6IlRpdHJlIjtzOjEzOiJIw6liZXJnZW1lbnRzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjk6IlBhcmMvSG9zdCI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7TjtzOjU6Ikljb25lIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZEltYWdlIjtzOjA6IiI7czoxNToiQmFja2dyb3VuZENvbG9yIjtzOjA6IiI7czo4OiJDbGFzc0NzcyI7czowOiIiO3M6NToiT3JkcmUiO3M6MjoiMjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czo5OiJQYWdlVGl0cmUiO047czoxNToiUGFnZURlc2NyaXB0aW9uIjtOO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiMTQiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX19fWk6MzthOjMxOntzOjM6IlVybCI7czo3OiJQcm9maWxlIjtzOjU6IlRpdHJlIjtzOjY6IlByb2ZpbCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMjoiUGFyYy9Qcm9maWxlIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiI1MCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIwIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjk6IlBhZ2VUaXRyZSI7TjtzOjE1OiJQYWdlRGVzY3JpcHRpb24iO047czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319fQ==');
        }

    private function createGroupContact($role){
        //creation du groupe revendeur
        $g = genericClass::createInstance('Systeme','Group');
        $g->Nom = "[PARC] Accès contact";
        $g->Skin = "AngularAdmin";
        $g->AddParent($role);
        $g->Save();
        //création des menus
//        $g->importMenus('YTo1OntpOjA7YToyOTp7czozOiJVcmwiO3M6MTM6IlRhYmxlYXVEZUJvcmQiO3M6NToiVGl0cmUiO3M6MTU6IlRhYmxlYXUgZGUgYm9yZCI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNDoiUGFyYy9EYXNoYm9hcmQiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToxO2E6Mjk6e3M6MzoiVXJsIjtzOjE0OiJJbmZyYXN0cnVjdHVyZSI7czo1OiJUaXRyZSI7czoxNDoiSW5mcmFzdHJ1Y3R1cmUiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTk6IlBhcmMvSW5mcmFzdHJ1Y3R1cmUiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjEiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoxOiIwIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjQ6e2k6MDthOjI5OntzOjM6IlVybCI7czo3OiJDbGllbnRzIjtzOjU6IlRpdHJlIjtzOjc6IkNsaWVudHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvQ2xpZW50IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MjoiOTciO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyOTp7czozOiJVcmwiO3M6MTM6IlBlcmlwaGVyaXF1ZXMiO3M6NToiVGl0cmUiO3M6MTU6IlDDqXJpcGjDqXJpcXVlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9EZXZpY2UiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjUiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czoyOiI5NyI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MjthOjI5OntzOjM6IlVybCI7czo3OiJUaWNrZXRzIjtzOjU6IlRpdHJlIjtzOjc6IlRpY2tldHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvVGlja2V0IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIxMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjI6Ijk3IjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aTozO2E6Mjk6e3M6MzoiVXJsIjtzOjY6InJlc2N1ZSI7czo1OiJUaXRyZSI7czoxMToiTW9kZSByZXNjdWUiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MjE6IlBhcmMvUmV2ZXJzZUNvbm5leGlvbiI7czo3OiJGaWx0ZXJzIjtzOjA6IiI7czoxNjoiUHJlZml4ZUNvZGViYXJyZSI7czowOiIiO3M6NDoiQWlkZSI7czowOiIiO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIyMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjI6Ijk3IjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319fX1pOjI7YToyOTp7czozOiJVcmwiO3M6NToiQ2xvdWQiO3M6NToiVGl0cmUiO3M6MTQ6IlByb2R1aXRzIGNsb3VkIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjg6IlBhcmMvV2ViIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiI1IjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTo0OntpOjA7YToyOTp7czozOiJVcmwiO3M6NzoiU2VydmVycyI7czo1OiJUaXRyZSI7czo4OiJTZXJ2ZXVycyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxMToiUGFyYy9TZXJ2ZXIiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czozOiIxMDEiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyOTp7czozOiJVcmwiO3M6ODoiRG9tYWluZXMiO3M6NToiVGl0cmUiO3M6ODoiRG9tYWluZXMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTE6IlBhcmMvRG9tYWluIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoxOiIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MzoiMTAxIjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToyO2E6Mjk6e3M6MzoiVXJsIjtzOjU6IkVtYWlsIjtzOjU6IlRpdHJlIjtzOjY6IkVtYWlscyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNToiUGFyYy9Db21wdGVNYWlsIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIxMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwMSI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fWk6MzthOjI5OntzOjM6IlVybCI7czoxMToiSGViZXJnZW1lbnQiO3M6NToiVGl0cmUiO3M6MTM6IkjDqWJlcmdlbWVudHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6OToiUGFyYy9Ib3N0IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIyMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwMSI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX19aTozO2E6Mjk6e3M6MzoiVXJsIjtzOjk6IkRvY3VtZW50cyI7czo1OiJUaXRyZSI7czo5OiJEb2N1bWVudHMiO3M6OToiU291c1RpdHJlIjtzOjA6IiI7czo0OiJMaWVuIjtzOjA6IiI7czo3OiJBZmZpY2hlIjtzOjE6IjEiO3M6NToiQWxpYXMiO3M6MTQ6IlBhcmMvRG9jdW1lbnRzIjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIxMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjE6IjAiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6Mzp7aTowO2E6Mjk6e3M6MzoiVXJsIjtzOjg6IkZhY3R1cmVzIjtzOjU6IlRpdHJlIjtzOjg6IkZhY3R1cmVzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjEyOiJQYXJjL0ZhY3R1cmUiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjE6IjAiO3M6ODoiTWVudUhhdXQiO3M6MToiMCI7czo3OiJNZW51QmFzIjtzOjE6IjAiO3M6MTM6Ik1lbnVQcmluY2lwYWwiO3M6MToiMSI7czoxMToiTWVudVNwZWNpYWwiO3M6MToiMCI7czoxMDoiQXV0b1N1YkdlbiI7czoxOiIwIjtzOjU6IlRpdGxlIjtzOjA6IiI7czoxMToiRGVzY3JpcHRpb24iO3M6MDoiIjtzOjg6IktleXdvcmRzIjtzOjA6IiI7czo1OiJJbWFnZSI7czowOiIiO3M6ODoiVGVtcGxhdGUiO3M6MToiMCI7czoxMDoiTWVudVBhcmVudCI7czozOiIxMDYiO3M6MTA6Ik9iamVjdFR5cGUiO3M6NDoiTWVudSI7czo0OiJub3RlIjtpOjEwO3M6NjoiTW9kdWxlIjtzOjc6IlN5c3RlbWUiO3M6NToiTWVudXMiO2E6MDp7fX1pOjE7YToyOTp7czozOiJVcmwiO3M6OToiQ29tbWFuZGVzIjtzOjU6IlRpdHJlIjtzOjk6IkNvbW1hbmRlcyI7czo5OiJTb3VzVGl0cmUiO3M6MDoiIjtzOjQ6IkxpZW4iO3M6MDoiIjtzOjc6IkFmZmljaGUiO3M6MToiMSI7czo1OiJBbGlhcyI7czoxNDoiUGFyYy9Db21tYW5kZXMiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjIwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjEiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MzoiMTA2IjtzOjEwOiJPYmplY3RUeXBlIjtzOjQ6Ik1lbnUiO3M6NDoibm90ZSI7aToxMDtzOjY6Ik1vZHVsZSI7czo3OiJTeXN0ZW1lIjtzOjU6Ik1lbnVzIjthOjA6e319aToyO2E6Mjk6e3M6MzoiVXJsIjtzOjg6IkNvbnRyYXRzIjtzOjU6IlRpdHJlIjtzOjg6IkNvbnRyYXRzIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjEzOiJBYnRlbC9Db250cmF0IjtzOjc6IkZpbHRlcnMiO3M6MDoiIjtzOjE2OiJQcmVmaXhlQ29kZWJhcnJlIjtzOjA6IiI7czo0OiJBaWRlIjtOO3M6NToiSWNvbmUiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kSW1hZ2UiO3M6MDoiIjtzOjE1OiJCYWNrZ3JvdW5kQ29sb3IiO3M6MDoiIjtzOjg6IkNsYXNzQ3NzIjtzOjA6IiI7czo1OiJPcmRyZSI7czoyOiIzMCI7czo4OiJNZW51SGF1dCI7czoxOiIwIjtzOjc6Ik1lbnVCYXMiO3M6MToiMCI7czoxMzoiTWVudVByaW5jaXBhbCI7czoxOiIxIjtzOjExOiJNZW51U3BlY2lhbCI7czoxOiIwIjtzOjEwOiJBdXRvU3ViR2VuIjtzOjE6IjAiO3M6NToiVGl0bGUiO3M6MDoiIjtzOjExOiJEZXNjcmlwdGlvbiI7czowOiIiO3M6ODoiS2V5d29yZHMiO3M6MDoiIjtzOjU6IkltYWdlIjtzOjA6IiI7czo4OiJUZW1wbGF0ZSI7czoxOiIwIjtzOjEwOiJNZW51UGFyZW50IjtzOjM6IjEwNiI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX19aTo0O2E6Mjk6e3M6MzoiVXJsIjtzOjc6IlByb2ZpbGUiO3M6NToiVGl0cmUiO3M6NjoiUHJvZmlsIjtzOjk6IlNvdXNUaXRyZSI7czowOiIiO3M6NDoiTGllbiI7czowOiIiO3M6NzoiQWZmaWNoZSI7czoxOiIxIjtzOjU6IkFsaWFzIjtzOjEyOiJQYXJjL1Byb2ZpbGUiO3M6NzoiRmlsdGVycyI7czowOiIiO3M6MTY6IlByZWZpeGVDb2RlYmFycmUiO3M6MDoiIjtzOjQ6IkFpZGUiO047czo1OiJJY29uZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRJbWFnZSI7czowOiIiO3M6MTU6IkJhY2tncm91bmRDb2xvciI7czowOiIiO3M6ODoiQ2xhc3NDc3MiO3M6MDoiIjtzOjU6Ik9yZHJlIjtzOjI6IjUwIjtzOjg6Ik1lbnVIYXV0IjtzOjE6IjAiO3M6NzoiTWVudUJhcyI7czoxOiIwIjtzOjEzOiJNZW51UHJpbmNpcGFsIjtzOjE6IjAiO3M6MTE6Ik1lbnVTcGVjaWFsIjtzOjE6IjAiO3M6MTA6IkF1dG9TdWJHZW4iO3M6MToiMCI7czo1OiJUaXRsZSI7czowOiIiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJLZXl3b3JkcyI7czowOiIiO3M6NToiSW1hZ2UiO3M6MDoiIjtzOjg6IlRlbXBsYXRlIjtzOjE6IjAiO3M6MTA6Ik1lbnVQYXJlbnQiO3M6MToiMCI7czoxMDoiT2JqZWN0VHlwZSI7czo0OiJNZW51IjtzOjQ6Im5vdGUiO2k6MTA7czo2OiJNb2R1bGUiO3M6NzoiU3lzdGVtZSI7czo1OiJNZW51cyI7YTowOnt9fX0=');
    }
	/**
	 * Initilisation des variables globales disponibles pour la boutique
	 */
	private function initGlobalVars(){
        if (!Sys::$User->Public){
            //initialisation client si connecté
            $Cls = Sys::$User->getOneChild('Client');
            if ($Cls){
                $this->_ParcClient = $Cls;
                $GLOBALS["Systeme"]->registerVar("ParcClient",$this->_ParcClient);
            }else{
                //test si revendeur
                $Rvs = Sys::$User->getOneChild('Revendeur');
                if ($Rvs){
                    $this->_ParcRevendeur = $Rvs;
                    $GLOBALS["Systeme"]->registerVar("ParcRevendeur",$this->_ParcRevendeur);
                } else {
                    //test si contact
                    $ct = Sys::$User->getOneChild('Contact');
                    if ($ct){
                        $this->_ParcClient = $ct;
                        $GLOBALS["Systeme"]->registerVar("ParcContact",$this->_ParcClient);
                    }
                }
            }
        }
	}

	/**
     * Renouvellement des certificats
     */
	public function renewCertificates () {
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour des certificats expirés';
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Parc';
        $task->Demarre = true;
        $task->TaskFunction = 'Renew';
        $task->Save();
        $task->createActivity('Recherche des certificats à mettre à jour');
	    //recherche des hébergements à renouveller avec une expiration dans les prochaines 24 heures
        $aps = Sys::getData('Parc','Apache/Ssl=1&&SslMethod=Letsencrypt&SslExpiration<'.(time()+86400));
        //pour chaque apache on crée une tache pour renouveller le certificat
        foreach ($aps as $a){
            $act = $task->createActivity('Mise à jour du certificat des domaines '.$a->getDomains());
            if ($a->enableSsl()) $act->Terminate(true);
            else{
                $act->addDetails(print_r($a->Error,true));
                $act->Terminate(false);
            }
        }
        $task->Termine = true;
        $task->Save();
    }
    public static function Renew(){
        $parc = Sys::getModule('Parc');
        $parc->renewCertificates();
    }
    /**
     * Execution des taches
     */
    public  function executeTasks() {
        $start = time();
        Sys::autocommitTransaction();
        while(time()<$start+240){
            //empty query cache
            Sys::$Modules['Parc']->Db->clearLiteCache();
            $t = Sys::getOneData('Parc','Tache/Demarre=0&DateDebut<'.time(),0,1);
            //execution de la tache
            if ($t)
                $t->Execute($t);
            else sleep(1);
        }
        return true;
    }
    public static function Execute(){
        $parc = Sys::getModule('Parc');
        $parc->executeTasks();
    }
    /**
     * checkState
     * Vérification des instances
     */
    public  function createCheckState($task=null) {
        echo "check State\r\n";
        $nb = Sys::getCount('Parc','Instance/Enabled=1');
        $it = abs($nb/100)+1;
        //recherche des hébergements à renouveller avec une expiration dans les prochains 30 jours
        for ($i=0;$i<=$it;$i++) {
            $aps = Sys::getData('Parc', 'Instance/Enabled=1', $i*100, 100);
            //pour chaque instance on crée une tache pour vérifier l'etat
            foreach ($aps as $a) {
                if ($a->createCheckStateTask()) echo "--> checkState $a->Nom \r\n";
                //else print_r($a->Error);
            }
        }
        if ($task){
            $task->Termine = true;
            $task->Save();
        }
        return true;
    }
    public static function checkState($task = null){
        $parc = Sys::getModule('Parc');
        return $parc->createCheckState($task);
    }
    public static function createCheckStateTask(){
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Lancement d\'une vérification globale des instances';
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Parc';
        $task->TaskFunction = 'checkState';
        $task->Save();
    }

    /**
     * backup
     * Sauvegarde des hébergements
     */
    public  function createBackup($task=null) {
        echo "backup\r\n";
        $nb = Sys::getCount('Parc','Host');
        $it = abs($nb/100)+1;
        //recherche des hébergements à renouveller avec une expiration dans les prochains 30 jours
        for ($i=0;$i<=$it;$i++) {
            $aps = Sys::getData('Parc', 'Host', $i*100, 100);
            //pour chaque instance on crée une tache pour vérifier l'etat
            foreach ($aps as $a) {
                if ($a->createBackupTask()) echo "--> backup $a->Nom \r\n";
                //else print_r($a->Error);
            }
        }
        if ($task){
            $task->Termine = true;
            $task->Save();
        }
        return true;
    }
    public static function backup($task = null){
        $parc = Sys::getModule('Parc');
        return $parc->createBackup($task);
    }
    public static function createBackupTask(){
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Lancement de la création des taches de backup';
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Parc';
        $task->TaskFunction = 'backup';
        $task->Save();
    }
    /**
     * UTILS FUNCTIONS
     */
    static public function localExec( $command, $activity = null,$total=0){
        /*exec( $command,$output,$return);
        if( $return ) {
            throw new RuntimeException( "L'éxécution de la commande locale a échoué. commande : ".$command." \n ".print_r($output,true));
        }
        return implode("\n",$output);*/
        $proc = popen("$command 2>&1 ; echo Exit status : $?", 'r');
        $complete_output = "";
        while (!feof($proc)){
            $buf     = fread($proc, 4096);
            //cas borg
            if (preg_match('#^([0-9\.]+) MB #',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1])*100000000)/$total;
                $activity->setProgression($progress);
            }
            $complete_output .= $buf;
        }
        pclose($proc);
        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        if( $matches[0] !== "0" ) {
            throw new RuntimeException( $complete_output, (int)$matches[0] );
        }
        return str_replace("Exit status : " . $matches[0], '', $complete_output);
    }
    static public function getMyIp(){
        $output = Parc::localExec('/usr/sbin/ifconfig');
        preg_match('#inet ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)#',$output,$out);
        return $out[1];
    }
    /**
     * Incoming Connexion
     *
     */
    static public function incoming () {
        $rc = genericClass::createInstance('Parc','ReverseConnexion');
        $rc->Nom ='Connexion entrante depuis '.$_SERVER['SERVER_ADDR'];
        $rc->Save();
        echo "port=".$rc->PortEcoute."\ncode=".$rc->CodeConnexion."\n".ReverseConnexion::getFilePath();
    }

    /**
     * est ce qu'il y un technicien en ecoute
     * @param code de connexion GET
     */
    static public function listening () {
        $code=isset($_GET['code'])?$_GET['code']:false;
        $text=isset($_GET['text'])?$_GET['text']:'';
        if (!$code) return "0";
        //recherche de la connexion
        $rc = Sys::getOneData('Parc','ReverseConnexion/CodeConnexion='.$code);
        if (!$rc) return "0";

        //définition de l'état
        //1 -> waiting
        //2 -> connecting
        //3 -> connecté
        //4 -> deconnexion -> busy false
        //5 -> erreur -> delete
        //6 -> terminé -> delete
        if (isset($_GET['state'])){
            $state = $_GET['state'];
            switch ($state){
                case 0:
                    $rc->Status = 'Initialisation '.$text;
                    $rc->Save();
                    break;
                case 1:
                    $rc->Status = 'En attente de connexion '.$text;
                    $rc->Save();
                    break;
                case 2:
                    $rc->Status = 'En cours de connexion '.$text;
                    $rc->Save();
                    break;
                case 3:
                    $rc->Status = 'Connecté '.$text;
                    $rc->Save();
                    break;
                case 4:
                    $rc->Status = 'Déconnecté '.$text;
                    $rc->Busy = false;
                    $rc->Save();
                    break;
                case 5:
                    $rc->Status = 'Erreur '.$text;
                    $rc->Busy = false;
                    $rc->Save();
                    break;
                case 6:
                    $rc->Status = 'Terminé '.$text;
                    $rc->Delete();
                    return "0";
                    break;
            }
        }
        if ($rc->Busy) return "1";
        else return "2";
    }

    /*****************
     * CHECK SSL STATE
     *******************/
    public static function checkSslState($task = null){
        $parc = Sys::getModule('Parc');
        return $parc->createCheckSslStateTask($task);
    }
    /**
     * Créatio de la tache qui crééera toutes les taches de vérification
     */
    public static function createCheckSslStateTask(){
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Lancement d\'une vérification globale des certificats';
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Parc';
        $task->TaskFunction = 'createAllCheckSslTask';
        $task->Save();
    }
    /**
     * createCheckSslTask
     * Creation de la tache de vérification des certificats ssl
     */
    public static function createAllCheckSslTask($task=null) {
        $pxs = Sys::getData('Parc','Apache/Ssl=1',0,100000);
        $i=1;
        foreach ($pxs as $px){
            $t = genericClass::createInstance('Parc', 'Tache');
            $t->Type = 'Fonction';
            $t->Nom = 'Vérification du certificat SSL ' . $px->ApacheServerName;
            $t->TaskModule = 'Parc';
            $t->TaskObject = 'Apache';
            $t->TaskId = $px->Id;
            $t->TaskFunction = 'checkCertificate';
            $t->Save();
            $task->addRetour('- ('.$i.')' . $px->ApacheServerName);
            $i++;
        }
        $task->Save();
    }
    /*****************
     * MAINTENANCE MANAGEMENT
     *******************/
    public static function startMaintenance($task = null){
        $parc = Sys::getModule('Parc');
        return $parc->startMaintenanceTask($task);
    }
    /**
     * Opération de maintenance
     */
    public static function startMaintenanceTask($task = null){
        //suppression des taches vielle de plsu d'une heure
        $GLOBALS['Systeme']->Db[0]->query('DELETE FROM `'.MAIN_DB_PREFIX.'Parc-Tache` WHERE tmsCreate<'.(time()-(3600*12)).' AND Erreur=0 AND TaskType="Vérification";');
        $GLOBALS['Systeme']->Db[0]->query('REPAIR TABLE `'.MAIN_DB_PREFIX.'Parc-Tache`;');
        $GLOBALS['Systeme']->Db[0]->query('OPTIMIZE TABLE `'.MAIN_DB_PREFIX.'Parc-Tache`;');
        //suppression des activités vielle de plsu d'une heure
        $GLOBALS['Systeme']->Db[0]->query('DELETE FROM `'.MAIN_DB_PREFIX.'Parc-Activity` WHERE tmsCreate<'.(time()-(3600*12)).';');
        $GLOBALS['Systeme']->Db[0]->query('REPAIR TABLE `'.MAIN_DB_PREFIX.'Parc-Activity`;');
        $GLOBALS['Systeme']->Db[0]->query('OPTIMIZE TABLE `'.MAIN_DB_PREFIX.'Parc-Activity`;');
    }
}
