#!/bin/bash
rm Home/*/.cache -Rf
rm Home/*/.sessions -Rf
rm Modules/*/.Db.cache -f
rm Skins/*/.cache -Rf
find . -name "*.mini.*.jpg" -delete
find . -name "*.limit.*.jpg" -delete
