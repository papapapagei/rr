#!/usr/bin/perl
print "content-type: text/plain\n\n";
qx"/usr/bin/mysqldump --host=db2187.1und1.de --password=schatten978 --user=dbo310397634 db310397634 > export.sql";
print "MySQL-Export gestartet\n";