#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.

SCRIPT_PATH=$(realpath "$(dirname "${BASH_SOURCE[0]}")/cron.php")
CRON_CMD="0 0 * * * php $SCRIPT_PATH >> /var/log/xkcd_cron.log 2>&1"

CRON_EXISTS=$(crontab -l 2>/dev/null | grep -F "$SCRIPT_PATH")

if [ -z "$CRON_EXISTS" ]; then
    (crontab -l 2>/dev/null; echo "$CRON_CMD") | crontab -
    echo "CRON job set up to run daily at midnight."
    echo "Command: $CRON_CMD"
else
    echo "CRON job already exists."
fi