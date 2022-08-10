If Then Else
============

This processor allows you to utilise branching logic, i.e. if a thing is
`true`, then do things. else do something else.

Inputs
------

* lhs
    * The first comparison value (Left-Hand Side).
* rhs
    * The second comparison value (Right-Hand Side).
* operator
    * The comparison operator (`==`, `!=`, `>`, `>=`, `<`
      , `<=`)
* strict (optional)
    * Perform a strict comparison (compare lhs & rhs variable types - default
      is `true`)
* then
    * Processors to perform the logic if the comparison result is true.
* else
    * Processors to perform the logic if the comparison result is false.

Processing order
----------------

`lhs`, `operator`, `rhs`, `strict` values are calculated first,
the result of the comparison is done and then the correct `then`/`else`
path is added to the processing stack.
