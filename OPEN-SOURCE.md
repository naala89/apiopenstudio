free version
============

Code only, setting up the infrastructure is up to the user/devs.

config
------

change Config.php to be a single file, with Config.example.php 

newsletter/mailing list
-----------------------

web
---

- location /html/admin
- url: /admin
- can set custom banner and icon
- lightweight responsive css
  - https://speckyboy.com/responsive-lightweight-css-frameworks/
  - https://bulma.io
  - https://purecss.io/
  - https://css.gd/

### pages

#### install

- tidy install pages html layout

#### update

- need a page with script to deal with version upgrades that involve composer or db updates.
- only visible to developer
- new db field - version

#### login

- use datagator native resources for validation.
- visible to all

#### accounts

- owner can view all accounts
- administrator can only view the accounts they are associated with
- select list at the top to filter by account
- list users assigned to accounts and their roles (link to account)
- owner role able to add/edit/delete accounts in modals

#### resources

- owner can view all resources
- administrator can only view resources for the accounts they are associated with
- developer can only view resources for the accounts they are associated with
- select list at the top to filter by account
- if no account selected then list of all resources
- developer role can view/add/edit/delete resources in modals

#### users

- visible to owner & administrator
- list all users and their roles and accounts they are associated with
- add/edit/delete users in modals

processors
----------

- create user processor to deal with user crud
- create user_role processor to deal with user/role crud
- create role processor to deal with role crud
  - Update admin and install to use these crud services
- review processor grouping

nginx
-----

create a sample conf file for nginx installs

docker
------

create a docker package that can use the repo with nginx and mysql

composer
--------

create a packagist/composer instance so that instancing and updating is easy

github
------

create an open repo with ticketing

gulp
----

create a gulp script to compile sass out of the html dir

questions
---------

- how will we deal with updates
  - what form will they be in
  - how will the code interpret them
  - what updates can we expect
- should we re-architect the app so that it can accommodate custom and 3rd party processors
  - how can this we done
  - would they be in a separate repository
- how do we handle 3rd party processors
  - packagist?
- custom css, logo and header title

paid version
============

dockers set up in EC2 on behalf of the user.

sandbox

ddos protection

desktop/tablet app for creating/editing/deleting resources

datagator will only contain the tried amd tested features from the open source version

web
--- 

### additional elements

- dashboard for tracking api use

github
------

create a closed repo with ticketing

docker
------

create a docker package that can use the repo with nginx and mysql

questions
---------

- how costly is ddos protection
- need to finish the tracking stream solution
- need to create the app

roadmap
=======

- angular app
- enterprise
    - dashboard
    - remade inteface cms
    - api tracking
- rebuilt core haskell