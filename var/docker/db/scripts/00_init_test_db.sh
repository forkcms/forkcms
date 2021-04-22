#!/bin/bash

##############################################################
# Initialize a second database used for integration testing  #
##############################################################
MYSQL_DATABASE_TEST="${MYSQL_DATABASE}_test"

echo "Creating database ${MYSQL_DATABASE_TEST}..."
mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "create database ${MYSQL_DATABASE_TEST};"

echo "Importing fixtures into database ${MYSQL_DATABASE_TEST}..."
mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" ${MYSQL_DATABASE_TEST} < ../test_db.sql

echo "Granting all privileges for user $MYSQL_USER on database ${MYSQL_DATABASE_TEST}..."
mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "GRANT ALL ON \`${MYSQL_DATABASE_TEST}\`.* TO '$MYSQL_USER'@'%' ;"

echo "Succesfully initialized database ${MYSQL_DATABASE_TEST}!"
