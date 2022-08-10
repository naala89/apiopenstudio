Introduction
============

The following pages give details on the `logic` processors, and how to use
them.

Processing order
----------------

ApiOpenStudio uses depth-first iteration of the node tree internally (the
process section of a resource metadata). This is a way of parsing a node tree,
where the code starts calculations at the end nodes and works towards the final
root node (which provides the final result).

This is because the results of each processor needs to flow down as an input for
its parent processor.

In the case of `Logic` processors (conditional processors), this would
potentially be wasteful (or impossible) processing, where we do not want to
needlessly process unused logic branches (`if_then_else`), 
