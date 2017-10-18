
#BACKUP_DIR=/var/lib/pgsql/9.5/backups/

#/usr/bin/pg_dump -h 127.0.0.1 -U sherwint_sherwin -c sherwint_tntmobile > $BACKUP_DIR"sherwint_tntmobile-"`date +\%Y-\%m-\%d-\%H-\%M`.sql

#/usr/bin/psql -h 127.0.0.1 -U sherwint_sherwin sherwint_tntmobile < /srv/www/tnt.dev/maintenance/maintenance.sql

PGDUMP=`which pg_dump`
PSQL=`which psql`
TAR=`which tar`
GZIP=`which gzip`
BACKUP_DIR=/var/lib/pgsql/9.5/backups/
MSQL=/srv/www/tnt.dev/maintenance/maintenance.sql
CURDATE=`date +\%Y-\%m-\%d-\%H-\%M`
DBNAME=sherwint_tntmobile
IPADD=127.0.0.1
USERNAME=sherwint_sherwin
SQLFILE=$BACKUP_DIR$DBNAME"-"$CURDATE.sql
TGZFILE=$SQLFILE.tgz
GZFILE=$SQLFILE.gz
DOPG="$PGDUMP -h $IPADD -U $USERNAME -c $DBNAME | $GZIP > $GZFILE"
DOTAR="$TAR czvf $TGZFILE $SQLFILE"
DOPSQL="$PSQL -h $IPADD -U $USERNAME $DBNAME < $MSQL"
echo $DOPG
$PGDUMP -h $IPADD -U $USERNAME -c $DBNAME | $GZIP > $GZFILE
echo $DOPSQL
$PSQL -h $IPADD -U $USERNAME $DBNAME < $MSQL
