#!/bin/bash
        R11=`cat /sys/class/net/ens160/statistics/rx_bytes`
        T11=`cat /sys/class/net/ens160/statistics/tx_bytes`
        R21=`cat /sys/class/net/ens192/statistics/rx_bytes`
        T21=`cat /sys/class/net/ens192/statistics/tx_bytes`
        sleep 1
        R12=`cat /sys/class/net/ens160/statistics/rx_bytes`
        T12=`cat /sys/class/net/ens160/statistics/tx_bytes`
        R22=`cat /sys/class/net/ens192/statistics/rx_bytes`
        T22=`cat /sys/class/net/ens192/statistics/tx_bytes`
        TBPS1=`expr $T12 - $T11`
        RBPS1=`expr $R12 - $R11`
        TBPS2=`expr $T22 - $T21`
        RBPS2=`expr $R22 - $R21`
	TBPS=`expr $TBPS1 + $TBPS2`
	RBPS=`expr $RBPS1 + $RBPS2`
        TKBPS=`expr $TBPS / 128`
        RKBPS=`expr $RBPS / 128`
        echo "$TKBPS|$RKBPS"
