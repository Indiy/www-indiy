#!/bin/bash

SRC_DB=madd3v.com
DEST_DB=madcom
SRC_HOST=api.madd3v.com
DEST_HOST=madprod.cghmds5s4dwn.us-east-1.rds.amazonaws.com
DATE=$(date -u +"%Y_%m_%d_%H_%M_%S")
BACKUP_FILE="madcom_backup_$DATE.sql"
SQL_DIFF_FILE="madcom_sqldiff.sql"

PASSWORD=sad7823jba613kjbasuyeq

mysqldump --user=dbdiff --password=$PASSWORD --host=$DEST_HOST $DEST_DB > $BACKUP_FILE
mysqldiff --tolerant --user=dbdiff --password=$PASSWORD --host1=$DEST_HOST --host2=$SRC_HOST $DEST_DB $SRC_DB > $SQL_DIFF_FILE
