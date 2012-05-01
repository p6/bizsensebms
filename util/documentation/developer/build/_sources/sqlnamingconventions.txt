SQL Naming Conventions
--------------------------

At this point, we support only MySQL database. At some point in the future, we will support PostgreSQL and SQLite.

* Table names and column names are in lower case. Words are separated by an underscore. Example, `call_status`
* Tables names are singular. Example, `user` is correct. `users` is incorrect
* Text columns, ie, data types being text, char, varchar, longtext, etc have collation utf8_general_ci
* All tables have a primary key
* The auto increment column is named <table_name_id>. This data type for this column is unsigned int.
* All tables use the InnoDB storage engine
* Verbose names are preferred. For example, `invice` is preferred over `inv`
* Collation for all tables must be utf8_unicode_ci



