#!/bin/bash
echo Script name: $0
echo $# arguments 
if [ $# -ne 2 ]; 
then 
    echo "Usage: $0 OLD_SITE NEW_SITE"
    exit
fi
echo "test"

