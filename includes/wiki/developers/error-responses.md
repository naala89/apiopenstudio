Error responses
===============

All errors should be trapped by an exception and return a standard error object, which is translated into the correct output format by ApiOpenStudio.

example (json):

```
{
  "error": {
    "id": "example_function_id",
    "code": 7,
    "message": "The application does not exist."
  }
}
```

example (xml):

```
<error>
  <id>example_function_id</id>
  <code>7</code>
  <message>The application does not exist</message>
</error>
```

* **id** is the function in the resource where the error occurred. 
* **code** is the error type.
* **message** is a human friendly error message.

Error codes
-----------

0. Core error
1. Processor format error
2. DB error
3. Invalid API call
4. Authorisation error
5. External error
6. Invalid processor input
7. Invalid application

HTML status codes
-----------------

Where possible, HTML status codes will also match the above error codes, which will usually be a 200 (Success) or 400 (Bad Request)

