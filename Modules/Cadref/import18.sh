#!/bin/bash
sed -e "s/'\\\\'\\\\''/''/g" /home/paul/tmp/cadref18.sql > /tmp/cadref18.sql
mysql -u syntech --password=21wyisey << EOF
drop database if exists cadref18;
create database cadref18;
EOF
mysql -u syntech --password=21wyisey < /tmp/cadref18.sql
mysql -u syntech --password=21wyisey < /home/paul/wks/kbabtel/kobeye/Modules/Cadref/import18.sql

