HOST=localhost
USER=inscriptions-afl
PASSWORD=inscriptions-afl
DBNAME=inscriptions-afl

# Put in a Makefiel.local password to production environment
-include Makefile.local

MYSQLDUMPOPTS=--default-character-set=utf8 
MYSQLDUMP=mysqldump5 ${MYSQLDUMPOPTS}
MYSQL=mysql ${MYSQLDUMPOPTS}

NOW=$(shell date +%d-%m-%Y_%R)

DUMPSDIR=dumps
DATAFILE=dumps/data.sql
#DATAFILE=inhabit.sql

test:
	echo ${NOW}

dump:
	${MYSQLDUMP} -h${HOST} -p${PASSWORD} -u${USER} ${DBNAME} > ${DUMPSDIR}/last_${NOW}.sql
	cp ${DUMPSDIR}/last_${NOW}.sql ${DUMPSDIR}/last.sql

structure:
	${MYSQLDUMP} -d -h${HOST} -p${PASSWORD} -u${USER} ${DBNAME} > ${DUMPSDIR}/structure_${NOW}.sql

data:
	${MYSQLDUMP} --skip-comments --no-create-info --complete-insert -h${HOST} -p${PASSWORD} -u${USER} ${DBNAME} > ${DUMPSDIR}/data.sql
	cp ${DUMPSDIR}/data.sql ${DUMPSDIR}/data_${NOW}.sql

## The following are destructive, but acts only on local host !!!

upload_last:
	${MYSQL} -hlocalhost -p${PASSWORD} -u${USER} -D${DBNAME} < structure.sql
	${MYSQL} -hlocalhost -p${PASSWORD} -u${USER} -D${DBNAME} < dumps/data.sql

upload_structure:
	${MYSQL} -hlocalhost -p${PASSWORD} -u${USER} -D${DBNAME} < structure.sql
	${MYSQL} -hlocalhost -p${PASSWORD} -u${USER} -D${DBNAME} < ${DATAFILE}

# edit:
#	${MYSQL} -hlocalhost -p${PASSWORD} -u${USER} -D${DBNAME} < dumps/last.sql

### The following are destructive !!!


update_structure: dump data
	${MYSQL} -h${HOST} -p${PASSWORD} -u${USER} -D${DBNAME} < structure.sql
	${MYSQL} -h${HOST} -p${PASSWORD} -u${USER} -D${DBNAME} < ${DUMPSDIR}/data.sql

upload_structure_with_data:
	${MYSQL} -h${HOST} -p${PASSWORD} -u${USER} -D${DBNAME} < structure.sql
	${MYSQL} -h${HOST} -p${PASSWORD} -u${USER} -D${DBNAME} < ${DATAFILE}


restore_from_last:
	${MYSQL} -h${HOST} -p${PASSWORD} -u${USER} -D${DBNAME} < dumps/last.sql

