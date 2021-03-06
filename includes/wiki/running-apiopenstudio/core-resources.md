Core resources
==============

The details of all resources provide by core.

All URL's are in the format: ```https://<api.domain.com>/<account_name>/<application_name>/<uri>/<uri_params>```

All get, post and uri params should be url encoded.

<table>
    <tr>
        <th style="width: fit-content; white-space: nowrap">Name</th>
        <th style="width: fit-content; white-space: nowrap">Description</th>
        <th style="width: fit-content; white-space: nowrap">Machine name</th>
        <th style="width: fit-content; white-space: nowrap">URI</th>
        <th style="width: fit-content; white-space: nowrap">Method</th>
        <th style="width: fit-content; white-space: nowrap">Post params</th>
        <th style="width: fit-content; white-space: nowrap">Get params</th>
        <th style="width: fit-content; white-space: nowrap">URI params</th>
        <th style="width: fit-content; white-space: nowrap">Body</th>
        <th style="width: fit-content; white-space: nowrap">Example</th>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Account create</td>
        <td style="width: fit-content; white-space: nowrap">Create an account</td>
        <td style="width: fit-content; white-space: nowrap">account_create</td>
        <td style="width: fit-content; white-space: nowrap">account</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">name -> account name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/account</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Account delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete an account</td>
        <td style="width: fit-content; white-space: nowrap">account_delete</td>
        <td style="width: fit-content; white-space: nowrap">account</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> account ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/account/34</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Account read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a single or multiple accounts</td>
        <td style="width: fit-content; white-space: nowrap">account_read</td>
        <td style="width: fit-content; white-space: nowrap">account</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">keyword -> filter by name<br />order_by -> order results<br />direction -> order direction</td>
        <td style="width: fit-content; white-space: nowrap">0 -> account ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/account/64<br />/apiopenstudio/core/account?kyword=my%20dept&order_by=name&direction=asc</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Account update</td>
        <td style="width: fit-content; white-space: nowrap">Rename an account</td>
        <td style="width: fit-content; white-space: nowrap">account_update</td>
        <td style="width: fit-content; white-space: nowrap">account</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> account ID<br />1 -> new name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/account/43/new&20name</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Application create</td>
        <td style="width: fit-content; white-space: nowrap">Create an application</td>
        <td style="width: fit-content; white-space: nowrap">application_create</td>
        <td style="width: fit-content; white-space: nowrap">application</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">accid -> account ID<br />name -> application name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/application</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Application delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete an application</td>
        <td style="width: fit-content; white-space: nowrap">application_delete</td>
        <td style="width: fit-content; white-space: nowrap">application</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> application ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/application/102</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Application read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a single or multiple applications</td>
        <td style="width: fit-content; white-space: nowrap">application_read</td>
        <td style="width: fit-content; white-space: nowrap">application</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">account_id -> filter by accid<br />application_id -> filter by appid<br />keyword -> filter by keyword<br />order_by -> order the results<br />direction -> asc or desc</td>
        <td style="width: fit-content; white-space: nowrap">0 -> application ID<br />1 -> account ID<br />1 -> application name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/application/34<br />/apiopenstudio/core/application//15<br />/apiopenstudio/core/application///my%20resource<br />/apiopenstudio/core/application?keyword=foobar</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Application update</td>
        <td style="width: fit-content; white-space: nowrap">Update an application</td>
        <td style="width: fit-content; white-space: nowrap">application_update</td>
        <td style="width: fit-content; white-space: nowrap">application</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/application/33/3/new%name</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Functions</td>
        <td style="width: fit-content; white-space: nowrap">Fetch details of all functions</td>
        <td style="width: fit-content; white-space: nowrap">functions</td>
        <td style="width: fit-content; white-space: nowrap">functions</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> machine name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/functions<br />/apiopenstudio/core/functions/account_read</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Invite accept</td>
        <td style="width: fit-content; white-space: nowrap">User invite accepted using a token</td>
        <td style="width: fit-content; white-space: nowrap">invite_accept</td>
        <td style="width: fit-content; white-space: nowrap">user/invite/accept</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> User invite accept token</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/invite/accept/4i75we4br7ywn3rcfnwi8vyes5tivynesrotyvn</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Invite delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a user invite</td>
        <td style="width: fit-content; white-space: nowrap">invite_delete</td>
        <td style="width: fit-content; white-space: nowrap">invite</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> user invite ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/invite/4765</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Invite read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch user invites</td>
        <td style="width: fit-content; white-space: nowrap">invite_read</td>
        <td style="width: fit-content; white-space: nowrap">invite</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">iid -> filter by invite ID<br />email -> filter by email<br />order_by -> order by column<br />direction -> order by direction<br />offset -> skip n rows<br />limit -> fetch n rows</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/invite?email=foo%40bar.com</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Invite send</td>
        <td style="width: fit-content; white-space: nowrap">Invite a user or users</td>
        <td style="width: fit-content; white-space: nowrap">invite_send</td>
        <td style="width: fit-content; white-space: nowrap">user/invite</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">email -> email/s</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/invite</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Password reset</td>
        <td style="width: fit-content; white-space: nowrap">Reset a user password</td>
        <td style="width: fit-content; white-space: nowrap">password_reset</td>
        <td style="width: fit-content; white-space: nowrap">password/reset</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">email -> email to send the password reset to<br />token -> password reset token<br />password -> new password</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/password/reset</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource create</td>
        <td style="width: fit-content; white-space: nowrap">Create a resource</td>
        <td style="width: fit-content; white-space: nowrap">resource_create</td>
        <td style="width: fit-content; white-space: nowrap">resource</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">name -> name of the resource<br />description -> description of the resource<br />appid -> application ID to associate the resource with<br />method -> request method<br />uri -> resource URI<br />ttl -> caching time<br />format -> json ot yaml format<br />meta -> the security, process and output metadata</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource </td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a resource</td>
        <td style="width: fit-content; white-space: nowrap">resource_delete</td>
        <td style="width: fit-content; white-space: nowrap">resource</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> resource ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource/453</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource export</td>
        <td style="width: fit-content; white-space: nowrap">Export a resource</td>
        <td style="width: fit-content; white-space: nowrap">resource_export</td>
        <td style="width: fit-content; white-space: nowrap">resource/export</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> export format<br />1 -> resource ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource/export/yaml/1345</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource import</td>
        <td style="width: fit-content; white-space: nowrap">Import a resource from a file</td>
        <td style="width: fit-content; white-space: nowrap">resource_import</td>
        <td style="width: fit-content; white-space: nowrap">resource/import</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">resource_file -> form post type file</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource/import</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a single or multiple resources</td>
        <td style="width: fit-content; white-space: nowrap">resource_read</td>
        <td style="width: fit-content; white-space: nowrap">resource</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">resid -> filter by resource ID<br />accid -> filter by account  ID<br />appid -> filter by application ID<br />keyword -> filter by keyword<br />order_by -> order by column<br />direction -> order by direction</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource?accid=1</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Resource update</td>
        <td style="width: fit-content; white-space: nowrap">Update a resource</td>
        <td style="width: fit-content; white-space: nowrap">resource_update</td>
        <td style="width: fit-content; white-space: nowrap">resource</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">JSON encoded string of a resource file contents.</td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/resource</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Role create</td>
        <td style="width: fit-content; white-space: nowrap">Create a new role</td>
        <td style="width: fit-content; white-space: nowrap">role_create</td>
        <td style="width: fit-content; white-space: nowrap">role</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">name -> role name</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/role</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Role delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a new role</td>
        <td style="width: fit-content; white-space: nowrap">role_delete</td>
        <td style="width: fit-content; white-space: nowrap">role</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> role ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/role/33</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Role read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a single or multiple roles</td>
        <td style="width: fit-content; white-space: nowrap">role_read</td>
        <td style="width: fit-content; white-space: nowrap">role</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">keyword -> filter by keyword<br />order_by -> order by column<br />direction -> order by direction</td>
        <td style="width: fit-content; white-space: nowrap">0 -> account ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/role/54<br />/apiopenstudio/core/role</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Role update</td>
        <td style="width: fit-content; white-space: nowrap">Update a role</td>
        <td style="width: fit-content; white-space: nowrap">role_update</td>
        <td style="width: fit-content; white-space: nowrap">role</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">JSON containing role ID and new name, e.g.<br />{"rid": 6, "name": "ive changed"}</td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/role</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User create</td>
        <td style="width: fit-content; white-space: nowrap">Create a user</td>
        <td style="width: fit-content; white-space: nowrap">user_create</td>
        <td style="width: fit-content; white-space: nowrap">user</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">username<br />password<br />active<br />honorific<br />name_first<br />name_last<br />email<br />company<br />website<br />street_address<br />suburb<br />city<br />state<br />country<br />postcode<br />phone_mobile<br />phone_work</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a user</td>
        <td style="width: fit-content; white-space: nowrap">user_delete</td>
        <td style="width: fit-content; white-space: nowrap">user</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> User ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/1034</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a user</td>
        <td style="width: fit-content; white-space: nowrap">user_read</td>
        <td style="width: fit-content; white-space: nowrap">user</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">uid -> filter by user ID<br />username -> filter by username<br />email -> filter by email<br />keyword -> filter by keyword<br />orderBy -> Order results by column<br />direction -> order by direction</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/login</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User update</td>
        <td style="width: fit-content; white-space: nowrap">Update a user</td>
        <td style="width: fit-content; white-space: nowrap">user_update</td>
        <td style="width: fit-content; white-space: nowrap">user</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> User ID to update</td>
        <td style="width: fit-content; white-space: nowrap">JSON encoded string, containing the following optional attributes:<ul><li>username</li><li>password</li><li>active</li><li>honorific</li><li>name_first</li><li>name_last</li><li>email</li><li>company</li><li>website</li><li>street_address</li><li>suburb</li><li>city</li><li>state</li><li>country</li><li>postcode</li><li>phone_mobile</li><li>phone_work</li></ul></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User login</td>
        <td style="width: fit-content; white-space: nowrap">Login a user to ApiOpenStudio using username/password</td>
        <td style="width: fit-content; white-space: nowrap">user_login</td>
        <td style="width: fit-content; white-space: nowrap">login</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">username<br />password</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/login</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User role create</td>
        <td style="width: fit-content; white-space: nowrap">Assign a role to a user for an account/application</td>
        <td style="width: fit-content; white-space: nowrap">user_role_create</td>
        <td style="width: fit-content; white-space: nowrap">user/role</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">uid -> user ID<br />accid -> account ID<br />appid -> application ID<br />rid -> role ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/role</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User role delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a role for a user for an account/application</td>
        <td style="width: fit-content; white-space: nowrap">user_role_delete</td>
        <td style="width: fit-content; white-space: nowrap">user/role</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> user/role ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/role/1024</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">User Role read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch roles</td>
        <td style="width: fit-content; white-space: nowrap">user_role_read</td>
        <td style="width: fit-content; white-space: nowrap">user/role</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">uid -> filter by user ID<br />accid -> filter by account ID<br />appid -> filter by application ID<br />rid -> filter by role ID<br />order_by -> order results by column<br />direction -> order by direction</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/user/role?accid=45&rid=4</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Var store create</td>
        <td style="width: fit-content; white-space: nowrap">Create a var store variable</td>
        <td style="width: fit-content; white-space: nowrap">var_store_create</td>
        <td style="width: fit-content; white-space: nowrap">var_store</td>
        <td style="width: fit-content; white-space: nowrap">POST</td>
        <td style="width: fit-content; white-space: nowrap">appid -> application ID<br />key -> variable keyname<br />val -> variable value</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/var_store</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Var store delete</td>
        <td style="width: fit-content; white-space: nowrap">Delete a var store variable</td>
        <td style="width: fit-content; white-space: nowrap">var_store_delete</td>
        <td style="width: fit-content; white-space: nowrap">var_store</td>
        <td style="width: fit-content; white-space: nowrap">DELETE</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> variable ID</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/var_store/256</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Var store read</td>
        <td style="width: fit-content; white-space: nowrap">Fetch a single or multiple var store variables where the user has access to the applications vars</td>
        <td style="width: fit-content; white-space: nowrap">var_store_read</td>
        <td style="width: fit-content; white-space: nowrap">var_store</td>
        <td style="width: fit-content; white-space: nowrap">GET</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">vid -> filter by variable ID<br />appid -> filter by application ID<br />keyword -> filter by keyword<br />order_by ->order results by column<br />direction -> order by direction</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/var_store?appid=758&order_by=key&direction=desc</td>
    </tr>
    <tr>
        <td style="width: fit-content; white-space: nowrap">Var store update</td>
        <td style="width: fit-content; white-space: nowrap">Update a var store variable</td>
        <td style="width: fit-content; white-space: nowrap">var_store_update</td>
        <td style="width: fit-content; white-space: nowrap">var_store</td>
        <td style="width: fit-content; white-space: nowrap">PUT</td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap"></td>
        <td style="width: fit-content; white-space: nowrap">0 -> variable ID</td>
        <td style="width: fit-content; white-space: nowrap">The value to place in the variable</td>
        <td style="width: fit-content; white-space: nowrap">/apiopenstudio/core/var_store</td>
    </tr>
</table>
