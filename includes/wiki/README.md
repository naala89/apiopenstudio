Wiki
====

Introduction
------------

This wiki is written entirely in markdown,
compiled by bookdown and deployed to the wiki servers.

Compiling the wiki locally
--------------------------

### Docker

Checkout the docker repo at [api_open_studio_docker](https://gitlab.com/john89/api_open_studio_docker)

Uncomment The two wiki containers:

* bookdown
* wiki

Ensure that you have add the following to ```/etv/hosts```:

    127.0.0.1   wiki.apiopenstudio.local

Run ```docker-compose up -d```

This will automatically compile, deploy and serve the wiki HTML from ```public/wiki/```

Visit [wiki.apiopenstudio.local](https://wiki.apiopenstudio.local)

### Manually compile

Run the following command from the repo root directory:

    ./vendor/bin/bookdown includes/wiki/bookdown.json
