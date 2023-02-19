#!/bin/sh

/usr/sbin/apachectl -D BACKGROUND
service cron start & tail -f /cron.log