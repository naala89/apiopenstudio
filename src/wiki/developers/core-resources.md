Core resources
==============

TODO

The details of all resources provide by core.

All URL's are in the format: ```https://<api.domain.com>/<account_name>/<application_name>/<uri>/<uri_params>```

All get, post and uri params should be url encoded.

| operation | resource name | URL | method | Post params | Get params | URI params | Example |
| ------ | ------ | ------ | ------ | ------ | ------ | ------ | ------ |
| User login | user login | /login | post | username<br />password | None | None | https://my.api.com/apiopenstudio/core/login |
| account create | Account create/update | /account | post | accountName | None | None | https://my.api.com/apiopenstudio/core/account |
| account fetch | Account read | /account | get | None | accountName (if 'all' then all the accounts you have access to) | None | https://my.api.com/apiopenstudio/core/account |
| account update | Account create/update | /account | post | accountName<br />oldName | None | None | https://my.api.com/apiopenstudio/core/account |
| account delete | Account delete | /account | delete | None | Pos 0: <account_name> | None | https://my.api.com/apiopenstudio/core/account/my%20account |
