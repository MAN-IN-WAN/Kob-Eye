#!/bin/bash
ssh root@madeinchina.boutique "mysqldump -u madeinchina --password=21wyisey madeinchina" | mysql -u madeinchina --password=21wyisey -C madeinchina
