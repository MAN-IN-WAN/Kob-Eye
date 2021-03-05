#!/bin/bash
/usr/bin/wall 'Recompile Starting'
cd /home/inst-demo-vetoccitan/www/Skins/VetoccitanT3/ReactSrc
npm run build > /tmp/toto 2>&1 
/usr/bin/wall 'Recompile Done' 
exit 1
