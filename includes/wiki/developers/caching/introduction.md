Introduction
============

ApiOpenStudio allows you to connect it with a (single or cluster) Memcached or
Redis servers.

It will automatically store processing results in the cache and fetch them on
the next call, until the cache time is stale.

It is possible to cache an entire resource result, or individual processor
results.

