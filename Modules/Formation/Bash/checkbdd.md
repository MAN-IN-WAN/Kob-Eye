#!/bin/sh
### BEGIN INIT INFO
# Provides:          checkbdd
# Required-Start:    $all
# Required-Stop:
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Verification de la consistyance de la bdd
### END INIT INFO

PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/opt/bin

# If you need to source some other scripts, do it here
. /lib/init/vars.sh
. /lib/lsb/init-functions

case "$1" in
    start)
        CORRUPT=$(/usr/bin/mysqlcheck -u root -pzH34Y6u5 formation | grep error | wc -l)
        if [ $CORRUPT -gt 0 ]; then
            log_begin_msg "Injection de la base formation car corrompue"
            #injection sauvegarde de la base
            /usr/bin/mysql -u root -pzH34Y6u5 -e 'DROP DATABASE formation;'
            /usr/bin/mysql -u root -pzH34Y6u5 -e 'CREATE DATABASE formation;'
            /usr/bin/mysql -u root -pzH34Y6u5 formation < /var/www/formation.bck.sql
            log_end_msg $?
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
