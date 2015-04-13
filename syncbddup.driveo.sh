#!/bin/bash
#ssh root@madeinchina.boutique "mysqldump -u driveo --password=21wyisey driveo" | mysql -u driveo --password=21wyisey -C driveo
mysqldump -u driveo --password=21wyisey driveo | ssh root@ecluse.m-w.solutions "mysql -u driveo --password=21wyisey -C driveo"
