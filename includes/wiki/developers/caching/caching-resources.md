Caching resources
=================

To set a cache for a resource result, set the `ttl` value in the head of your
resource yaml to a value (in seconds).

ApiOpenStudio evaluates for an active cache system, and then to see if the
resource has an active cache entry.

The cache key is generated automatically: `resource_<resource_id>`.

Examples
--------

No caching:

    name: resource with no cache
    description: example resource head section without any cache
    uri: example/no-cache
    method: get
    appid: 2
    ttl: 0

Cache for 5 minutes:

    name: resource with 5m cache
    description: example resource head section that si cached for 5 minutes
    uri: example/5m-cache
    method: get
    appid: 2
    ttl: 300
