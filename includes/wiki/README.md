Wiki
====

Introduction
------------

This wiki is written entirely in markdown, compiled by bookdown and deployed to
the wiki server.

Compiling the wiki locally
--------------------------

### Compile

To compile locally, run the following command from the repo root directory:

    export CSS_BOOTSWATCH=spacelab && export CSS_PRISM=prism && MENU_LOGO=/img/api_open_studio_logo_name_colour.png && php ./vendor/bin/bookdown includes/wiki/bookdown.json

### Docker

Checkout the docker repo at [Docket GitHub][docker_github] or [Docket GitLab][docker_gitlab].

Uncomment The two wiki containers:

* wiki

Ensure that you have add the following to ```/etv/hosts```:

    127.0.0.1   wiki.apiopenstudio.local

Run ```docker-compose up -d```

This will automatically deploy and serve the wiki HTML from ```public/wiki/```

Visit [wiki.apiopenstudio.local][wiki_local].

[docker_github]: https://github.com/naala89/api_open_studio_docker

[docker_gitlab]: https://gitlab.com/apiopenstudio/apiopenstudio_docker_dev

[wiki_local]: https://wiki.apiopenstudio.local
