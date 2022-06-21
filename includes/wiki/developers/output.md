Output
======

The output section is optional. You can use it to specify returning the
results in multiple ways.

If omitted, the result of the resource will be returned in the result.

Defining outputs
----------------

If no output section is defined in the resource, the result is automatically
returned in the resource call result.

If an output section is defined, the result will be only processed by the
elements in the output section.

If the output section is defined and you want to return the result in the
response as well as by an output processor, add an element to the output
section with the value `result`.

If the output section is defined and there is no `result` element, then the
result of the remote output processors will be returned
(`true` or an error response).

An example `output` section can look like this:

    output:
        -
            result
        -
            processor: json_remote
            id: example JSON Remote output
            filename: example.json
            transport: ApiOpenStudio\Plugins\TransportSftp
            parameters:
                host: 192.168.0.1
                root_path: "/mnt/example/json/"
                username: sftp_user
                password: secret

The first element in the `output` section will cast the result to whatever is
in the request `Accept` header and return the result in the API call response.

The second element in the `output` section uses the `json_remote` output
processor: the output will be cast to json, and it then uses SFTP
transport to transfer the file to the server/location specified in the
`parameters` section.

### Response Output

Returning the result in a resource call is the default, and is dynamically cast
to the preferred format (i.e. XML, JSON, text, etc) by the resource caller's
`Accept` header.

If you inspect the code, in `includes/Output/`, you will see the following
files:

* File.php
    * Depending on the API call, the file will either be returned as an
      attachment or in the body.
* Html.php
    * Cast the result data into a HTML document. If the result is a data
      structure (i.e. not XML or HTML), then the data will be converted to a
      data-list in the body of the result HTML document.
* Image.php
    * Attempt ast the result to an image format (base64). If the Mime sub-type in
      the request does not match the image type, an error will be returned.
* Json.php
    * Cast the result data to JSON format.
    * If the result was XML or HTML, the data structure will be:
        * Attributes will be elements of the parent with prefix`_`
        * Elements without a name (i.e. text elements from HTML) with have the name
          `#text`.
* OctetStream.php
    * Cast the result data to file format and return as an octet stream.
* Plain.php
    * Cast the result data to plain text format.
* Text.php
    * Cast the result data to plain text format.
* Xml.php
    * Cast the result data to XML format.

These do not need to be specified in the resource. They are dynamically called,
depending on the `Accept` header of the API call and will attempt to cast the
result data to the requested format.

### Remote output

In addition to returning the result in the response, you can also upload the
result to remote servers or generate an email from the data:

* email
    * Send the results in an email.
    * The body of the email is populated by the result of the API call.
* html_remote
    * Cast the results to HTML format and upload the result as a file to a
      remote server.
* image_remote
    * Cast the results to image format (base64) and upload the result as a file
      to a remote server.
* json_remote
    * Cast the results to JSON format and upload the result as a file to a
      remote server.
* text_remote
    * Cast the results to TXT format and upload the result as a file to a
      remote server.
* xml_remote
    * Cast the results to XML format and upload the result as a file to a
      remote server.

Core transport plugins
----------------------

In addition to casting the results to different types and uploading the remote
servers, ApiOpenStudio allows you to specify different transport methods. These
are not included in the core, because that would cause too many, potentially
redundant downloads and server dependencies.

Each transport has its own PHP module and server requirements, however they can
be easily installed from composer, and then defined in the `transport` section
in the resource definition (**Note:** the value for `transport` must be the
full namespaced string):

### FTP

Upload to a remote server using the FTP protocol.

    composer require apiopenstudio/transport_ftp

Namespace:

    ApiOpenStudio\Plugins\TransportAzureFtp

### SFTP

Upload to a remote server using the SFTP protocol.

    composer require apiopenstudio/transport_sftp

Namespace:

    ApiOpenStudio\Plugins\TransportSftp

### Amazon AWS S3

Upload to an AWS S3 bucket.

    composer require apiopenstudio/transport_s3

Namespace:

    ApiOpenStudio\Plugins\TransportS3

### Google Cloud

Upload to a google cloud bucket.

    composer require apiopenstudio/transport_google_cloud

Namespace:

    ApiOpenStudio\Plugins\TransportGoogleCloud

### Azure Blob

Upload to an Azure Blob.

    composer require apiopenstudio/transport_azure_blob

Namespace:

    ApiOpenStudio\Plugins\TransportAzureBlob
