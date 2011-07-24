#!/usr/bin/perl
print "content-type: text/plain\n\n";
qx"/usr/bin/mysql --host=db2187.1und1.de --password=schatten978 --user=dbo310397634 db310397634 < import.sql";
print "MySQL-Import gestartet\n";
