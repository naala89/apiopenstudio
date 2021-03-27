GaterData (former name) 0.1.0, 1 Dec 2015
=========================================

- Installed on Swellnet API

GaterData (former name) 0.2.0, 25 April 2016
============================================

- Improved global handling of error object
- New YAML importer in uploader
- Infinite loop detector
- Fixed output classes handling
- Incorporated Composer -- External packages --- Facebook --- Spyc --- AdoDB ---
  Pbkdf2 -- Autoload the core classes
- Implemented data model pattern for DB
- Fixed Cache class
- validate inputs now uses details attribute in processors to define
  cardinality, cardinality changed from [min, max] to '?', '*', n
- added ability to normalise result in Url processor
- improved Normalise class
- object processor working
- import/export/delete json and yaml working
- added normalisation option to the Url endpoint processor
- changed Object processor, so that if input is not an array, then all the
  fields and values sit at root
- import resources now has full error feedback to devs

GaterData (former name) 0.2.1, 3 May 2016
=========================================

- added way of defining specific processors in processor->details->accepts,
  e'g' 'processor varGet'
- removed Core\UserInterface, having multiple classes with the same name is
  confusing and extra abstraction was wastung resources
- User should not generate a new token when a valid one still exists
- completed Email output processor
- fixed multiple output delivery
- added GET/POST for multiple delivery
- finish processor descriptions
- change Api->parseType() to return 1st possible output type
- new ImportSwagger processor
- added VarBody (should the result be normalised?)
- added IfThenElse processor
- added Sort processor
- created VarPersistent and VarTemporary
- removed VarStore
- created Fragments section - this is a partial meta, allowing reuse of
  processor results - especially useful in IfThenElse to cut down on processing
  time
- change all resource meta 'validation' to 'security'

GaterData (former name) 0.3, 23 May 2016
========================================

Run "composer dump-autoload" on in the docroot after transferring files. Run "
composer update" on in the docroot after transferring files.

- unify $request and $resource into one object
- add crud processor for account, user & application
- added crud api calls for account, user & application
- fixed issues in new resource validation
- consolidated DB structure and data into one file
- added unit testing framework (codeception)
- removed validation that request var exist in VarGet, VarPost & VarRequest -
  this responsibility belongs in the parent processor
- fixed security processors not validating user_role.
- added acceptance tests
- changed how Object stores Field (no longer as list, but as associative array
- added Filter processor
- fixed config override

ApiOpenStudio v1.0.0-alpha-RC1
=============================

- Renamed the code to ApiOpenStudio
    - Docker used
    - Decoupled admin from the DB (API abstraction call only)
    - Implemented with GitLab CI runner
    - Wiki created
    - phpdoc implemented
    - Core totally rewritten again
    - All processors for core crud implemented
    - Admin interface implemented
    - Changed to use Monolog for logging
    - Changed to use Cascade for settings

ApiOpenStudio v1.0.0-alpha-RC2
=============================

- Separated the admin code from the API.
- Added user logout processor.
- Restructured the wiki pages.
- Unit tests running automatically on the CI.
- Create the bash DB install script for the headless API.
- Loomed the CI runner DB script to use the same scripts.
- Added core version.
- Added the implementation for DB updates if need be.
- Created the update script.
- Consolidated settings.yml and .env.
- Minor bug fixes.

ApiOpenStudio v1.0.0-alpha
===========================

- Additional script for updating.

ApiOpenStudio v1.0.0-alpha1
===========================

- Fixed release issue where core version was not updated in DB definitions.

ApiOpenStudio v1.0.0-alpha2
=========================

- Added support for alpha/beta/RC release tags to the update script.
- Replaced all references to 'function' with 'processor', to remove all
  ambiguity.
- Updated the docblock in all files to point to the correct copyright entity.

v1.0.0-beta1 (in progress)
==========================

- Updated .gitlab-ci so that tests run on all merge requests and deploy to
  wiki/phpdoc on merges.
- Updated notes for contributing
