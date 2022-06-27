Caching processors
==================

There are 2 optional attributes that can be added to a processor in a resource
definition:

* `cache_ttl`
  * The time in seconds to cache the processor result.
* `cache_id`
  * Override the automatically generated cache key - this allows expensive
  processor times to be shared between resources.
  * **NOTE:** Use with caution, this may produce unexpected resource results
  if not implemented carefully.

The automatically generated key: `processor_<resource_id>_<processor_id>`.

Usage
-----

It is not currently possible to cache logic processors.

It is possible to cache any processors in the `security`, `process` or `output`
sections. However, it is not recommended to cache any processors in the
`security` section, because this will potentially make any security checks for
the resource invalid.

Examples
--------

Cache the results of a processor for 1 minute:

    process:
        lhs:
            processor: var_get
            id: get var for key lhs 
            cache_ttl: 60
            key: lhs

Cache the results of a processor for 1 minute to be shared between resources:

    process:
        lhs:
            processor: var_get
            id: get var for key lhs 
            cache_ttl: 60
            cache_id: foobar
            key: lhs
