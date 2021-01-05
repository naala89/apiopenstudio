Resource definitions
====================

name
----

Mandatory. This is a human readable title of the resource.

description
-----------

Mandatory. This is a human readable description of the resource and what it does.

application
-----------

Mandatory. The application name that the resource is associated with. This is stored in the resource row as the application ID (appid).

account
-------

Mandatory. The account name that holds the application name. This is not stored in the DB, but is used in the full resource URL and also because multiple applications can have the same namne.

method
------

Mandatory. This is the REST method for the resource. Only 'get', 'post', 'put' and 'delete' are allowed.

uri
---

Mandatory. This is the URI part that is used to locate the resource.

ttl
---

Mandatory. This is the cache time for a resource (in microseconds). 0 indicates no caching.

security
--------

Optional. This allows you to add validation that a user has access to the resource.

process
-------

Mandatory. This is the body of the resource, where you define all the data logic that will take place, and the result will be passed to the output.

output
------

Optional. If no output section is defined then ApiOpenStudio will look for the Accept header to find the output type.

You can have multiple outputs defined, to allow you to return the process result in the response in an opinionated format and also to upload to remote locations or send in an email/s.

If you have multiple outputs, the response output is defined by not having any destination attributes.

If you define a response output, then the Accept header in th request will be ignored.

**Warning:** Having multiple response formats may result in unpredictable responses.

### Examples:

Always return the result in JSON format

    output:
        function: json
        id: output_response_json

Upload the result in XML format to a remote server. The response will contain ```true``` or ```false```, depending on the process success or failure.

    output:
        function: file
        id: output_file_remote_destination
        filename: apiopenstudio.xml
        destination: 'http://this.server.com/drop/box
        method: post
        options:
            - skip_status: true

Return the response in JSON format and upload in HTML format to a remote server

    output:
        -
            function: json
            id: output_response_json
        -
            function: xml
            id: output_xml_remote_destination
            filename: apiopenstudio.xml
            destination: 'http://this.server.com/drop/box
            method: post
            options:
                - skip_status: true
                
            