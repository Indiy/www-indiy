#!/bin/bash

SRC_DB=maddvcom_mysql
DEST_DB=madcom_mysql
DATE=$(date +"%Y_%m_%d_%H_%M_%S")
BACKUP_FILE="madcom_backup_$DATE.sql"
SQL_DIFF_FILE="madcom_sqldiff.sql"

mysqldump --user=dbdiff --password=password $DEST_DB > $BACKUP_FILE
mysqldiff --user=dbdiff --password=password $DEST_DB $SRC_DB > $SQL_DIFF_FILE

