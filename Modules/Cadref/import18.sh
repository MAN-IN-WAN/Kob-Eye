#!/bin/bash
sed -e "s/'\\\\'\\\\''/''/g" /home/paul/tmp/cadref18.sql > /tmp/cadref18.sql
mysql -u syntech --password=21wyisey << EOF
drop database if exists cadref18;
create database cadref18;
EOF
mysql -u syntech --password=21wyisey cadref18 < /tmp/cadref18.sql
mysql -u syntech --password=21wyisey kbabtel < /home/paul/wks/kbabtel/kobeye/Modules/Cadref/import18.sql

