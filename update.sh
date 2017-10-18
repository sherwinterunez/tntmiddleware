#!/bin/sh
/home/kiosk/mountfs.sh rw
cd /srv/www/tnt.dev
git pull
/home/kiosk/mountfs.sh ro
