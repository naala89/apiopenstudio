Hello world
===========

Requirements
------------

For this tutorial, you will need:

* An installed ApiOpenStudio project.
    * See <a href="/quick-start/setup.html" target="_blank">quick-start -> setup</a>
      and <a href="/installation/docker/developer-environment.html" target="_blank">
      Setup docker</a>
* Postman (<a href="https://www.postman.com/downloads/" target="_blank">Download</a>)
  or similar REST client.

Create a new account and application
------------------------------------

Click on "Accounts" in the menu or navigate to [accounts page][accounts_page].

Click on the Plus icon:

![Add account](images/account_add_button.png)

Name new account: "tutorial"

![Create tutorial account](images/create_account.png)

You have now created a new top level account:

![Tutorial account created](images/new_account.png)

Click on "Applications" in the menu or navigate
to [applications page][applications_page].

Click on the Plus icon to create a new application. Assign the application to
the "tutorial" account and call it "quick_start".

![Create application](images/create_application.png)

You have now created the "quick-start" application that our resource will belong
to:

![Application created](images/new_application.png)

Configure users and roles
-------------------------

### Create a developer role for the new application

Click on "User Roles" in the menu or navigate to [user roles page][user_roles_page].

Click on the plus icon and assign yourself the developer role for Account:
tutorial and application: quick_start.

![Create developer role](images/create_user_role.png)

You now have permission to create a resource for the newly created quick_start
application.

### Create a "Hello world!" resource

This resource will display "Hello world!" in the result in whatever format the
client requires, and will have security that requires an active token from a
user with a developer role. The authentication method will vbe bearer token.

#### Define the resource name, description, and URL

Fill out the following fields in the interface:

* Name: ```Hello world```
    * This is the title of the resource that appears
      in [resources page][resources_page].
* Description: ```A quick-start hello world resource```
    * This is the description of the resource that appears
      in [resources page][resources_page].
* Account: ```tutorial```
    * This assigns the resource to the account tutorial.
* Application: ```quick_start```
    * This assigns the resource to the application quick_start.
* Method: ```GET```
    * This declares the HTTP method.
* URI: ```hello/world```
    * This defines the URI fragment for the request that comes after /<account>
      /<application>/. 8 TTL: 30
    * This gives the resource a cache time of 30 seconds.

![Resource definition](images/resource_definition_1.png)

So far, we have defined a resource that can be called from (GET) 
[https://api.apiopenstudio.local/tutorial/quick_start/hello/world][hello_world].

However, it does nothing and has no security yet.

#### Define the security

Add the following snippet to the Security section:

    processor: token_role
    id: security
    token:
      processor: bearer_token
      id: security_token
    role: Developer

This calls the processor ```token_role```. We're giving the processor an ID name
of "security", so that if there are any bugs we can see where the error is in
the result.

The ```token_role``` processor requires 2 inpute:

* token - the requesting user's token.
* role - the role to validate the requesting user against.

```token``` will use another processor to pass its result into token_role. This
is ```bearer_token```. This will return the bearer token value from the request
header. We will assign this an ID name of "security_token".

```role``` will not require processing from another processor, because this does
not need to be dynamic. So we're using a static string: "Developer".

![Resource security](images/resource_definition_2.png)

#### Define the process

Add the following snippet to the Process section:

    processor: var_str
    id: process
    value: 'Hello world!'

This will use a single processor: ```var_str```. This processor returns the
value of a strictly typed string.

It's input value does not need to be dynamic here, so we're giving it a static
string value.

![Resource process](images/resource_definition_3.png)

#### Save

Click on the ```Upload``` button.

The resource will be parsed and checked for any error, and saved to the
database.

If you navigate back to [resources page][resources_page], you should see your
new resource.

![Resource created](images/resource_created.png)

If you click on the download button in the listing for ```Hello world``` and
select YAML format, it should look like this:

    name: 'Hello world'
    description: 'A quick-start hello world resource'
    uri: hello/world
    method: get
    appid: 2
    ttl: ''
    security:
        processor: token_role
        id: security
        token:
            processor: bearer_token
            id: security_token
        role: Developer
    process:
        processor: var_str
        id: process
        value: 'Hello world'

You can edit and upload this yaml file as you wish.

### Run the new resource

Open up your REST client

#### Get a new token for your user

* Method: POST
* URL: https://api.apiopenstudio.local/apiopenstudio/core/login
* Header:
    * Accept: application/json
* Body:
    * x-www-form-urlencoded
    * fields:
        * username: <username>
        * password: <password>

![User login request header](images/user_login_header.png)

![User login request body](images/user_login_body.png)

The result should be something similar to:

    {
        "token": "13ae430eb19a6651378e22e3a37de8cf",
        "uid": 2
    }

Copy the value for the token.

#### Run Hello world!

* Method: GET
* URL: https://api.apiopenstudio.local/tutorial/quick_start/hello/world
* Header:
    * Accept: application/json
    * Authorization: Bearer <token>

The result should be something similar to:

    "Hello world!"

![Hello world result](images/hello_world_result.png)

If we change the Accept value in the header to ```application/xml```, we will
get something similar to:

    <?xml version="1.0"?>
    <apiopenstudioWrapper>Hello world!</apiopenstudioWrapper>

Exercises
---------

1. Fetch a get variable and assign this to the var_str.
1. Set up a var in the admin interface, hello_world_string: "Hello world!"
    1. adapt the process section so that it fetches the
       var ```hello_world_string``` and assigns the value to the var_str.

[accounts_page]: https://admin.apiopenstudio.local/accounts

[applications_page]: https://admin.apiopenstudio.local/applications

[user_roles_page]: https://admin.apiopenstudio.local/user/roles

[resources_page]: https://admin.apiopenstudio.local/resources

[hello_world]: https://api.apiopenstudio.local/tutorial/quick_start/hello/world