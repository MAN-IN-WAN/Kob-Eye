#!/bin/sh
### BEGIN INIT INFO
# Provides:          majservice
# Required-Start:    $all
# Required-Stop:
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Mise à jour du systeme formation
### END INIT INFO

PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/opt/bin
FLAGFILE=/var/run/miseajourhisto

# If you need to source some other scripts, do it here
. /lib/init/vars.sh
. /lib/lsb/init-functions

case "$1" in
    start)


        #test internet connection
        nc -z 8.8.8.8 53  >/dev/null 2>&1
        online=$?
        if [ $online -eq 0 ]; then
            /usr/sbin/service mysql start
            cd /var/www

            log_begin_msg "Syncrhonisation des projets"
            /usr/bin/php cron.php app.deploiement.pro Formation/Projet/1/Synchro.cron >> $FLAGFILE
            /usr/bin/php cron.php app.deploiement.pro Formation/Projet/2/Synchro.cron >> $FLAGFILE
            log_end_msg $?

            log_begin_msg "Syncrhonisation des sessions"
            /usr/bin/php cron.php app.deploiement.pro Formation/Session/Synchro.cron >> $FLAGFILE
            log_end_msg $?

            log_begin_msg "Mise à jour du boitier"
            wget -O /root/maj.sh "http://edf.e-p.consulting/maj.sh"
            chmod +x /root/maj.sh
            /bin/sh /root/maj.sh
            log_end_msg $?
        else
            echo "Offline"
        fi

        exit 0
    ;;
    stop)
        log_begin_msg "Stopping the coolest service ever unfortunately"

        # do something to kill the service or cleanup or nothing

        log_end_msg $?
        exit 0
    ;;
    *)
        echo "Usage: /etc/init.d/<your script> {start|stop}"
        exit 1
        ;;
esac
