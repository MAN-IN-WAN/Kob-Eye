<?php

class IPWatcher extends genericClass{
    public function checkIp() {
        echo 'check ip '.$this->IP."\r\n";
        $this->checkScore($this->IP);
        $this->dnsbllookup($this->IP);
        parent::Save();
    }

    /**
     * checkScore
     * Vérifie le score
     * @param $ip
     * @return mixed|string
     */
    function checkScore($ip){
        echo ' - check score ==> ';
        //on initialise l'IP qui va accueillir le score
        $score = '--';

        //on inverse l'IP pour transformer 1.2.3.4 en 4.3.2.1
        $parts = explode('.', $ip);
        $parts = array_reverse($parts);
        $ip_reverse = implode('.', $parts);

        //soumission de l'IP inversée et récupération d'un tableau associatif
        $dns_record = dns_get_record($ip_reverse.'.score.senderscore.com');

        //si le tableau n'est pas vide, cela signifie qu'on a un score lié à cette IP et on le récupère après nettoyage de la chaîne, sinon cela signifie que Return Path n'a pas (encore ?) constaté de trafic sur votre IP
        if(!empty($dns_record)) {
            $score = str_replace('127.0.4.', '', $dns_record[0]['ip']);
            $this->Reputation = $score;
            $this->HasReputation = true;
            echo $score."\r\n";
        }else{
            echo "FAILED \r\n";
            $this->HasReputation = false;
        }
    }

    /**
     * dnsbllookup
     * Vérifie la blacklist
     * @param $ip
     */
    function dnsbllookup($ip){
        echo ' - dnslookup blackist'."\r\n";
        // Add your preferred list of DNSBL's
        $dnsbl_lookup = [
            "dnsbl-1.uceprotect.net" => "UceProtect",
            "dnsbl-2.uceprotect.net" => "UceProtect",
            "dnsbl-3.uceprotect.net" => "UceProtect",
            "dnsbl.sorbs.net" => "Sorbs",
            "zen.spamhaus.org" => "SpamHaus",
            "bl.spamcop.net" => "SpamHaus",
            "sbl.spamhaus.org" => "SpamHaus",
            "xbl.spamhaus.org" => "SpamHaus" ,
            "b.barracudacentral.org" => "Barracuda"
        ];
        $listed = "";
        if ($ip) {
            $reverse_ip = implode(".", array_reverse(explode(".", $ip)));
            foreach ($dnsbl_lookup as $host=>$prop) {
                echo '   - check dns rr '.$host."\r\n";
                if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
                    $this->{$prop} = true;
                }else{
                    $this->{$prop} = false;
                }
            }
        }
    }
    /**
     * check
     * Vérifie l'état des ips
     */
    public static function checkAllIp() {
        $ips = Sys::getData('MailIpWatcher','IPWatcher');
        foreach ($ips as $ip) {
            $ip->checkIp();
        }
    }

}