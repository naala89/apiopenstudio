Introduction
============

The admin Interface allows a user-friendly way of interacting and administering
with ApiOpenStudio,

It is decoupled and only interacts with ApiOpenStudio through API calls. This is
so that:

* There is only ever a single persistent socket connection to the DB
    * This save DB resources
    * Maximises the speed of the DB
    * Cuts down on the chances of table locks

It means that though this decoupling, if there are ever ant core table changes,
the amdin area does not need to be updated, because the API calls will remain
the same/

In addition, it is possible in the future to create several 'flavours' of admin,
and users are free to choose whichever admin they want.
