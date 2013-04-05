#!/bin/sh

# A shell script which can be used to run the check.php script via a scheduled task

/usr/local/bin/wget -O - http://127.0.0.1/pingability-lite/check.php?runCheck=on | grep -q 'All is well' > /dev/null
##[ $? -a -e /dev/speaker ] && echo "f" > /dev/speaker

## Add this to /etc/crontab, then killall -HUP cron to load the new line, to run this every 30 minutes:
#*/30    *       *       *       *       root    /root/pingability-lite.sh