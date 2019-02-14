<?php

class InstanceDomain extends genericClass {

    public function Save(){

        $instance = $this->getOneParent('Instance');
        $host = $instance->getOneParent('Host');
        if(!$host){
            $this->addError(array('Message'=>'Impossible de trouver l\'hébergement associé à cet espace web'));
            return false;
        }


        $apacheDev = $host->getOneChild('Apache/ProxyCache=0&&Ssl=0');
        if(!$apacheDev) $apacheDev = $instance->createApache();
        $apacheDevSsl = $host->getOneChild('Apache/ProxyCache=0&&Ssl=1');
        if(!$apacheDevSsl) $apacheDevSsl = $instance->createApache(1);
        $apacheProd = $host->getOneChild('Apache/ProxyCache=1&&Ssl=0');
        if(!$apacheProd) $apacheProd = $instance->createApache(0,1);
        $apacheProdSsl = $host->getOneChild('Apache/ProxyCache=1&&Ssl=1');
        if(!$apacheProdSsl) $apacheProdSsl = $instance->createApache(1,1);


        if($this->Active){
            if($this->Ssl){
                if($this->ProxyCache){
                    $apacheProdSsl->addDomain($this->Url);
                    $apacheDevSsl->delDomain($this->Url);
                }else{
                    $apacheDevSsl->addDomain($this->Url);
                    $apacheProdSsl->delDomain($this->Url);
                }
            } else{
                $apacheDevSsl->delDomain($this->Url);
                $apacheProdSsl->delDomain($this->Url);
            }

            if($this->ProxyCache){
                $apacheProd->addDomain($this->Url);
                $apacheDev->delDomain($this->Url);
            }else{
                $apacheDev->addDomain($this->Url);
                $apacheProd->delDomain($this->Url);
            }

        } else {
                $apacheDevSsl->delDomain($this->Url);
                $apacheProdSsl->delDomain($this->Url);
                $apacheDev->delDomain($this->Url);
                $apacheProd->delDomain($this->Url);

        }
        
        return parent::Save();
    }


}