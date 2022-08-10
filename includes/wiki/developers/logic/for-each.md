For...Each
==========

This processor allows calculations to be done on each element of a collection or
object.

Inputs
------

* input
    * The `colletion` or `object` to process.
* item_key_key
    * During the processing of the `input` element, the array/object key is
      stored in VarTemporary under this key string.
* item_val_key
    * During the processing of the `input` element, the array/object value
      is stored in VarTemporary under this key string.
* process_loop
    * The logic to be run for each item.
* process_after (optional)
    * The logic to be run after all items are processed.

Processing order
----------------

`input`, `item_key_key`, `item_val_key` are pre-calculated.

Each element in `input` is fetched in turn, stored as a `VarTemporary`
(using `<for_each_item id>.key` and `<for_each_item id>.key` keys), and
`process_loop` is run.

Once each element has been processed, if there is any logic to run in
`process_after`, then that logic will be processed.
