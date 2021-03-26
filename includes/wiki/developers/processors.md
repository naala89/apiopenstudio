Processors
==========

All processrs in Api Open Studio are self documenting.

This is available in the GUI, on the create aresource page on the RHS panel.

Example on a [local environment][create_resource]
-------------------------------------------------

![ApiOpenStudio][example_resource]

Here you can see:

* Processor name: Collection
* Processor descriptions: Collection contains multiple values, like an array or
  list.
* Processor machine name: collection
* Processor inputs
    * items
        * Input description:
        * Input cardinality: 0:*
            * Signifies 0 or many values are allowed in this input.
        * Input Literal allowed: true
            * The input value can be a literal or the output from another
              processor.
        * Input ufnctions allowed: All
            * There is no limit on the input processors
        * Input var types allowed: All
            * There is no limit on var types, like boolean, string, etc.
        * Input values allowed: All
            * Sometimes a procvessor may require a very sepcific set of input
              values. In this case, there is no limit.
        * Input default: None
            * There is no input default defined. If there is no input, then the
              response will be an empty collection (in this case).

Example of fetching the processor list via API
----------------------------------------------

This requires a valid login token.

    Method: GET
    URL: <domain>/apiopenstudio/core/processors/all
    Headers:
        Accept: application/json
        Authorization: Bearer <token>

Sample response:

    [
        {
            "name": "Accept an invite",
            "machineName": "invite_accept",
            "description": "Accept an invite to ApiOpenStudio.",
            "menu": "Admin",
            "input": {
                "token": {
                    "description": "The invite token.",
                    "cardinality": [
                        1,
                        1
                    ],
                    "literalAllowed": true,
                    "limitProcessors": [],
                    "limitTypes": [
                        "text"
                    ],
                    "limitValues": [],
                    "default": ""
                }
            }
        },
        {
            "name": "Account create",
            "machineName": "account_create",
            "description": "Create an account.",
            "menu": "Admin",
            "input": {
                "name": {
                    "description": "The name of the account. This must contain alphanumeric characters only.",
                    "cardinality": [
                        1,
                        1
                    ],
                    "literalAllowed": true,
                    "limitProcessors": [],
                    "limitTypes": [
                        "text"
                    ],
                    "limitValues": [],
                    "default": ""
                }
            }
        },
        ...
    ]

You can also request the definition for a single processor, using the machine
name:

    Method: GET
    URL: <domain>/apiopenstudio/core/processors/account_create
    Headers:
        Accept: application/json
        Authorization: Bearer <token>

Inspecting the processor code
-----------------------------

You can also inspect the code for the processor. These resise in:

* includes/Endpoint/
* includes/Output/
* includes/Processor/
* includes/Security/

The definitions are in the protected attribute:

    $details

[create_resource]: https://admin.apiopenstudio.local/resource/create

[example_resource]: images/example_processor_documentation.png
