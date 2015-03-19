#!/bin/bash
for i in `find . -regex ".*\-[0-9]+-[0-9]+-[0-9]+"` ; 
do 
	echo $i
	rm -f $i
done

#suppression des fichiers images mini
for i in `find . -regex ".*\.mini\.[0-9]+x[0-9]+\..*"` ;
do
	echo $i
        rm -f $i
done

#suppression des fichiers images limit
for i in `find . -regex ".*\.limit\.[0-9]+x[0-9]+\..*"` ;
do
	echo $i
        rm -f $i
done

