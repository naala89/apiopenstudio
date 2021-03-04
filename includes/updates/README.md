Update functions
================

The purpose of the update functions are to ensure that database remains up to
date with the latest core version installed.

Each file should be specific to a major/intermediate version, e.g. updates_2.3.

The script class defined in ```includes/scripts/update.php``` will loop through
each file in ```includes/updates``` directory, and call the functions of any
core version > than the current ```version``` in the ```core``` table in order
of version.

Current DB version
------------------

To find the current DB version:

    SELECT version FROM core;

This is always in the format ```([0-9]\.){2}[0-9]```, e.g.:

    1.0.34

This may occasionally lag behind the current version installed,
if there are no DB updates in prior releases.

Creating update functions
-------------------------

The update script uses the PHPDoc metadata to find the version for the update.
This is defined with the ```@version``` tag.

The values can include (or not) the 'v' character ```([vV])+\s?([0-9]\.){2}[0-9]``` :

    @version 1.1.0
    @version v1.1.0
    @version v 1.1.0
    @version V1.1.0
    @version V 1.1.0

All other function PHPDoc is as standard.

The update function is responsible for exiting with a meaningful message.

The function should be called something meaningful to the update,
and accepts a ADODB_mysqli connection object. e.g.

    /**
     * Add new example table with my_col column.
     *
     * @param ADODB_mysqli $db
     *
     * @version v1.1.43
     */
    function create_a_new_example_table(ADODB_mysqli $db) {
        $sql = "CREATE TABLE IF NOT EXISTS example (my_col varchar(255))";
        if ($db->execute($sql) === false) {
            echo "Error: something whent wrong!";
            exit;
        }
    }
