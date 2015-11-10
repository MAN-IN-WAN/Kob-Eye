#!/bin/sh
#****** Functions **********
maj () {
    if [ ! -e "/root/maj-$1.tar.gz" ]; then
        wget -O "/root/maj-$1.tar.gz"  "http://erdf.e-p.consulting/maj-$1.tar.gz"
        tar xvzf "/root/maj-$1.tar.gz" -C /var/www --overwrite

        #On change les droits
        chown formation:formation /var/www/* -R

    fi
}

#MAJ 1.0.0
VERSION=1.0.0
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    #ON créé le dossier manquant
    mkdir -p /var/www/Home/1/Formation/Fichier
    chown formation.formation /var/www/Home/1/* -R

    #changement de canal hostapd
    wget -O /etc/hostapd/hostapd.conf http://erdf.e-p.consulting/Formation/Bash/hostapd.htm
    service hostapd restart

    #modification du dhclient pour acélérer le boot
    wget -O /etc/dhcp/dhclient.conf http://erdf.e-p.consulting/Formation/Bash/dhclient.htm

    #suppression du script /etc/network/if-up.d/miseajour
    rm -f /etc/network/if-up.d/miseajour

    #ajout du service checkbdd
    wget -O /etc/init.d/checkbdd http://erdf.e-p.consulting/Formation/Bash/checkbdd.htm
    chmod +x /etc/init.d/checkbdd
    update-rc.d checkbdd defaults

    #ajout du service majservice
    wget -O /etc/init.d/majservice http://erdf.e-p.consulting/Formation/Bash/majservice.htm
    chmod +x /etc/init.d/majservice
    update-rc.d majservice defaults

    #modification des sudoers
    wget -O /etc/sudoers http://erdf.e-p.consulting/Formation/Bash/sudoers.htm

    #on redémarre
    reboot
fi


#MAJ 1.0.1
#Ajout du sélecteur de canal
VERSION=1.0.1
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    #modification des sudoers
    wget -O /etc/sudoers http://erdf.e-p.consulting/Formation/Bash/sudoers.htm
fi

#MAJ 1.0.2
#Ajout du sélecteur de canal
VERSION=1.0.2
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    #changement de canal hostapd
    wget -O /etc/hostapd/hostapd.conf http://erdf.e-p.consulting/Formation/Bash/hostapd.htm
    service hostapd restart

    #configuration du pilote
    wget -O /etc/modprobe.d/8192cu.conf http://erdf.e-p.consulting/Formation/Bash/8192cu.htm
fi

#MAJ 1.0.3
#Ajout de la synchro des régions
VERSION=1.0.3
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION
fi

#MAJ 1.0.4
#Saisie manuelle et correction intégrité session
VERSION=1.0.4
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    #maj du service majservice
    wget -O /etc/init.d/majservice http://erdf.e-p.consulting/Formation/Bash/majservice.htm
    chmod +x /etc/init.d/majservice

fi

#MAJ 1.0.5
#Correction de la mise à jour des regions
VERSION=1.0.5
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    #maj du service majservice
    wget -O /etc/init.d/majservice http://erdf.e-p.consulting/Formation/Bash/majservice.htm
    chmod +x /etc/init.d/majservice

fi

#MAJ 1.0.6
#Correction de l'utilisation du meme appareil
VERSION=1.0.6
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

fi

#MAJ 1.0.7
#Suppression du mode production de l'app
VERSION=1.0.7
if [ ! -e "/root/maj-$VERSION.tar.gz" ]; then
    maj $VERSION

    //on redemarre
    reboot
fi

#MAJ BDD
#RESET BDD
#if [ ! -e "/root/last.sql" ]; then
#    wget -O /root/last.sql http://erdf.e-p.consulting/last.tar.gz
#    mysql -u root -pzH34Y6u5 -e "DROP DATABASE formation;"
#    mysql -u root -pzH34Y6u5 -e "CREATE DATABASE formation;"
#    mysql -u root -pzH34Y6u5 formation < /root/last.sql
#fi