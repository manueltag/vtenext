export VTIGERCRM_ROOTDIR=`dirname "$0"`/..
export USE_PHP=php

cd $VTIGERCRM_ROOTDIR

$USE_PHP -f cron/CleanLogs.php
