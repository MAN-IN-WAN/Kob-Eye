#!/bin/bash



        R11=$([ -d "/sys/class/net/ens160" ] && cat /sys/class/net/ens160/statistics/rx_bytes || echo 0)
        T11=$([ -d "/sys/class/net/ens160" ] && cat /sys/class/net/ens160/statistics/tx_bytes || echo 0)
        R21=$([ -d "/sys/class/net/ens192" ] && cat /sys/class/net/ens192/statistics/rx_bytes || echo 0)
        T21=$([ -d "/sys/class/net/ens192" ] && cat /sys/class/net/ens192/statistics/tx_bytes || echo 0)
        sleep 1
        R12=$([ -d "/sys/class/net/ens160" ] && cat /sys/class/net/ens160/statistics/rx_bytes || echo 0)
        T12=$([ -d "/sys/class/net/ens160" ] && cat /sys/class/net/ens160/statistics/tx_bytes || echo 0)
        R22=$([ -d "/sys/class/net/ens192" ] && cat /sys/class/net/ens192/statistics/rx_bytes || echo 0)
        T22=$([ -d "/sys/class/net/ens192" ] && cat /sys/class/net/ens192/statistics/tx_bytes || echo 0)
        TBPS1=`expr $T12 - $T11`
        RBPS1=`expr $R12 - $R11`
        TBPS2=`expr $T22 - $T21`
        RBPS2=`expr $R22 - $R21`
	TBPS=`expr $TBPS1 + $TBPS2`
	RBPS=`expr $RBPS1 + $RBPS2`
        TKBPS=`expr $TBPS / 128`
        RKBPS=`expr $RBPS / 128`
        echo "$TKBPS|$RKBPS"
