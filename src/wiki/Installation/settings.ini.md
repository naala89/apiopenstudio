Config
------

The ```config/settings.ini``` file contains the settings for your envirnment.

### $_server

This array indicates what a server's role is. A server can only have one of three possible values:

* development
* staging
* production

The indexes in the array contain the hostname of the server, and the value contains the value of the role.

### everywhere()

The values set in this function apply to all server roles, however, these values can overridden in the role functions (see below) or in the URL if $_allow_override has been set to true (see below).

#### $defaultFormat

Sets the default output format for API calls, if no ```Accept``` header value is received

#### $tokenLife

Sets the life of API tokens. Use format used by [strtotime](http://php.net/manual/en/function.strtotime.php) (e.g. "```+1 day```")

#### $dirVendor

The directory where composer installs the 3rd party files. You should need to edit this.

#### $dirYaml

The directory where you store your yaml files. You should not need to change this.

### development(), staging() and production()

#### $debug

Standard debug level:

0. None
1. Low
2. Medium
3. High
4. Everything

#### $debugDb

Debug level for any cli scripts:

0. None
1. Low
2. Medium
3. High
4. Everything

#### $debugCLI

Debug level for Database calls and DB instantiation:

0. None
1. Low
2. Medium
3. High
4. Everything

#### $_allow_override

if true, an api caller can override any of the config settings by assigning values to them in the URL. This should never be true on production servers.

#### $debugInterface

Where the debug data will be output. There are only two possible values:
* LOG
* HTML

#### $cache

Set to true to enable caching on the server (opcode service automatically discovered), set to false to disable.

If you have multiple services available on the server, you instruct Datagator to use a specific opcode by setting this value to ```apc``` for APC or ```memcache``` for MemCache.

#### $dbdriver

The db driver, e.g. ```mysqli```.

#### $dbhost

The DB hostname, e.g. ```localhost```.

#### $dbname

The DB name.

#### $dbuser

The DB user.

#### $dbpass

The DB password.

#### $dboptions

See [ADOdb documentation](http://phplens.com/lens/adodb/docs-adodb.htm) for possible values.

#### $errorLog

Path to the system error log.

#### Miscellaneous settings

You can set any server scpecific settings with init_set, date_default_timezone_set, etc within these functions.

Caching
-------

If you set the $cache setting in config (see above) to true, you need to have installed APC or Memcache. The system will automatically discover which opcode service has been installed and use the correct one.