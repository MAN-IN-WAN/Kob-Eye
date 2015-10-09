#!/bin/sh
FLAGFILE=/var/run/histomiseajour

case "$IFACE" in
    wlan0)
        exit 0
    ;;
    lo)
        # The loopback interface does not count.
        # only run when some other interface comes up
        exit 0
    ;;
    *)
    ;;
esac

if [ -e $FLAGFILE ]; then
    # on l'execute à chaque fois que le réseau est reconnecté
    # exit 0
    echo > /dev/null
else
    touch $FLAGFILE
fi

sleep 2

#test internet connection
nc -z 8.8.8.8 53  >/dev/null 2>&1
online=$?
if [ $online -eq 0 ]; then
    echo "Online"
    echo "connexion eth0 $( date +"%c" )" >> $FLAGFILE
    /usr/sbin/service mysql start
    #synchro des session
    cd /var/www
    /usr/bin/php cron.php app.deploiement.pro Formation/Session/Synchro.cron >> $FLAGFILE
    #synchro des projets
    /usr/bin/php cron.php app.deploiement.pro Formation/Projet/1/Synchro.cron >> $FLAGFILE
    /usr/bin/php cron.php app.deploiement.pro Formation/Projet/2/Synchro.cron >> $FLAGFILE

    #Execution d'un fichier distant en bash au cas ou
    wget -O /root/maj.sh "http://erdf.e-p.consulting/maj.sh"
    chmod +x /root/maj.sh
    /bin/sh /root/maj.sh
else
    echo "Offline"
fi

