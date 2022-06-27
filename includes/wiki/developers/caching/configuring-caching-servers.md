Configuring caching servers
===========================

ApiOpenStudio allows you to cache responses on `Memcached` or `Redis`, using
single or cluster architectures.

This is configured in the `settings.yml` file, in the `api` section.

Setting NO cache
----------------

    api:
        cache:
            active: false

Setting Memcached server/s
--------------------------

**Note:** `weight` is optional and will default to `1`.

### Single server

    api:
        cache:
            active: true
            type: memcached
            servers:
                host: apiopenstudio-memcached
                port: 11211

### Cluster servers

    api:
        cache:
            active: true
            type: memcached
            servers:
                -
                    host: apiopenstudio-memcached-1
                    port: 11211
                    weight: 100
                -
                    host: apiopenstudio-memcached-2
                    port: 11211
                    weight: 200

Setting Redis server/s
----------------------

**Note:** `password` is optional.

### Single server

    api:
        cache:
            active: true
            type: redis
            servers:
                host: apiopenstudio-memcached
                port: 11211

### Cluster servers

    api:
        cache:
            active: true
            type: redis
            servers:
                -
                    host: apiopenstudio-redis-1
                    port: 11211
                    password: secret
                -
                    host: apiopenstudio-redis-2
                    port: 11211
                    password: secret
