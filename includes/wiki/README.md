Wiki
====

Introduction
------------

This wiki is written entirely in markdown,
compiled by bookdown and deployed to the wiki server.

Compiling the wiki locally
--------------------------

### Docker

Checkout the docker repo at [GitHub](https://github.com/naala89/api_open_studio)
or GitLab

Uncomment The two wiki containers:

* bookdown
* wiki

Ensure that you have add the following to ```/etv/hosts```:

    127.0.0.1   wiki.apiopenstudio.local

Run ```docker-compose up -d```

This will automatically compile, deploy and serve the wiki HTML from ```public/wiki/```

Visit [wiki.apiopenstudio.local](https://wiki.apiopenstudio.local)

### Manually compile

The root bookdown.json is set for
the bookdown container to compile, which has its own directory structure.

To compile locally, edit ```includes/wiki/bookdown.json```, and change the target path:

    "target": "../../public/wiki",

Run the following command from the repo root directory:

    ./vendor/bin/bookdown includes/wiki/bookdown.json
