----
-- phpLiteAdmin database dump (http://phpliteadmin.googlecode.com)
-- phpLiteAdmin version: 1.9.5
-- Exported: 3:39pm on July 20, 2017 (UTC)
-- database file: ./db/database.sqlite
----
BEGIN TRANSACTION;

----
-- Table structure for wwwsqldesigner
----
CREATE TABLE wwwsqldesigner (
                          keyword varchar(30) NOT NULL default '',
                          data TEXT ,
                          dt DATETIME DEFAULT CURRENT_TIMESTAMP ,
                          PRIMARY KEY ( keyword )
                        );

----
-- Data dump for wwwsqldesigner, a total of 0 rows
----

----
-- structure for index sqlite_autoindex_wwwsqldesigner_1 on table wwwsqldesigner
----
;
COMMIT;
