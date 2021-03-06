How ApiOpenStudio Works
=======================

ApiOpenStudio has several layers that it uses to define its resources and access
to them.

Only specific roles have the permissions to create, edit or delete elements from
these layers.

The base ApiOpenStdio package is a completely headless API, and is the core of
the suite. It is self-contained and provides all of the logic and resources that
you will need to run ApiOpenStudio. This is available at

* https://gitlab.com/john89/api_open_studio
* https://github.com/naala89/api_open_studio

There is another package that will enable you to administer the headless
ApiOpenStudio through an admin interface:

* [api_open_studio_admin](https://gitlab.com/john89/api_open_studio_admin)
    * An administration GUI interface designed to make it easy to manage your
      accounts, applications, users and resources, as well as an area where you
      can directly create, edit and delete resources.
    * This is decoupled from ApiOpenStudio codebase, and interacting with the
      API using core REST calls.
    * It is written in SlimPHP and JQuery.
    * A new version is in development, written in VueJS.

Accounts, Applications, Resources and functions
-----------------------------------------------

An account is a superset of applications. This allows you to create fine-grained
access to ApiOpenStudio and its resources for all levels of users and make
control of your API resources and customers very easy.

### Accounts

An account is group of applications. So in a business sense, an account can be a
client.

### Applications

Applications belong to an account.

These are like projects or applications that are associated with an account.

### Resources

These are individual REST resources that are associated with an application.

A resource is the main element that developers will use to interact with
ApiOpenStudio.

It defines everything about a resource, such as:

* Resource name.
* Resource description.
* URI
* Account and Application that the resources is associated with.
* Caching
* Resource security - none (completely open), oauth, bearer token, etc.
* The flow of data between functions from input/s to the final output.
* The format of the output, which can be JSON, XML, text, file, image, etc.

### Functions

Functions are the semantic blocks used in the processing of the incoming data.

You should not need to create any Function classes, thus eliminating the need
for time-consuming programming.

There are many functions available with ApiOpenStudio core, but you can add more
functions from the community, or even create custom ones yourself. These can all
me maintained using composer.
