#!/bin/bash


TICS=60 #Default 1 tic/sec for 60 seconds
TIMESTAMP=`date +%s`
KEEPLIMIT=$(($TIMESTAMP-3600))
OUTPUT=./$TIMESTAMP.nhlog

for f in ./*.nhlog; do
    base=$(basename $f)
    filename="${base%.*}"
    #echo 'aaaaaaa'$filename
    #echo 'bbbbbbb'$KEEPLIMIT
    if [ "$filename" -lt "$KEEPLIMIT" ]; then
        #echo $f
        rm -rf $f
    fi
done


sh -ic "{ sudo /usr/sbin/nethogs -tc $TICS &>$OUTPUT; }" 3>&1 2>/dev/null
echo $(readlink -f $OUTPUT)