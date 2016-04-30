Datagator 0.1.0, 1 Dec 2015
---------------------------
- Installed on Swellnet API

Datagator 0.2.0, 25 April 2016
------------------------------
- Improved global handling of error object
- New YAML importer in uploader
- Infinite loop detector
- Fixed output classes handling
- Incorporated Composer
-- External packages
--- Facebook
--- Spyc
--- AdoDB
--- Pbkdf2
-- Autoload the core classes
- Implemented data model pattern for DB
- Fixed Cache class
- validate inputs now uses details attribute in processors to define cardinality, cardinality changed from [min, max] to '?', '*', n
- added ability to normalise result in Url processor
- improved Normalise class
- object processor working
- import/export/delete json and yaml working
- added normalisation option to the Url endpoint processor
- changed Object processor, so that if input is not an array, then all the fields and values sit at root
- import resources now has full error feedback to devs

Datagator 0.2.1, Unfinished
---------------------------
- added way of defining specific processors in processor->details->accepts, e'g' 'processor varGet'
- removed Core\UserInterface, having multiple classes with the same name is confusing and extra abstraction was wastung resources
- User should not generate a new token when a valid one still exists
- completed Email output processor
- fixed multiple output delivery
- added GET/POST for multiple delivery
- finish processor descriptions
- change Api->parseType() to return 1st possible output type
- new ImportSwagger processor
- added VarBody (should the result be normalised?)
- added IfThenElse processor

TODO:
- 
- change format of processor meta (see frontend)
- Change all resource meta 'validation' to 'security'
-- Update API->_getValidation()
- Finish endpoint processors
- Validators for oAuth, etc
- Implement collections
- Add validation of var type result. This can be declared in $this->required
- Add unit tests (Phing or Jenkins?)
- New Relic or Datadog
- fix problem of not writing to system specified logs. This might be uin Debug::_display()
- rename VarStore to VarPersistent
- add check to VarPersistent to prevent accounts creating too many vars in the DB.

- Processors
-- create Sort processor
-- create processor Literal (is this needed?)
-- Should non sysadmins be able to create processors without validation?

- Resource monitoring
-- Store number number calls per resource history
--- Store call result (error or not)
--- 5 minute intervals for current hour
--- Hourly intervals prior to current hour for the current day
--- Daily intervals for current month (store a years worth)

- Flood defense
-- New Processor Flood
--- Processor doesn't sit in the Processor chain, but separate like validator
--- Array Whitelist
--- Array Blacklist
--- Int Number calls (default 5)
--- Int Time range (default 1 min)
--- Int Seconds to ban (default 1 hour)
-- New table ban
--- Store time, IP, rid

- Alerts
-- This is an array of Alert processors
-- Processors don't sit in the Processor chain, but separate like validator
-- New Processor AlertError
--- Number and Range have default values, but can be overriden for the resource
--- Int Number of errors before alert
--- String Range (Minute, Hours, Day)
--- Array (String) Email
-- New processor AlertMax
--- Number and Range have default values, but can be overriden for the resource (less than or equal to default, or custom if paid sub)
--- Int Number of calls before alert
--- String Range (Minute, Hours, Day)
--- Array (String) Email
-- New processor AlertFrequency
--- Number and Range have default values, but can be overriden for the resource (less than or equal to default, or custom if paid sub)
--- Int Number of calls before alert
--- String Range (Minute, Hours, Day)
--- Array (String) Email
