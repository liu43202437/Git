#!/bin/bash
BASE_URL=$(bash ./GetConfig.sh)
URL=${BASE_URL}cron/auto_reminder

while [ true ]
do
# Auto-Processing
	curl $URL
# Time Period 1 minute
	sleep 60
done
