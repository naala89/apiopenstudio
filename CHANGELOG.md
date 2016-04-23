Datagator 0.1.0, 1 Dec 2015
---------------------------
- Installed on Swellnet API

Datagator 0.2.0, Unfinished
---------------------------
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

TODO:
- change format of processor meta (see frontend)
- create processor Literal (see frontend)
- Change all resource meta 'validation' to 'security'
-- Update API->_getValidation()
- Finish endpoint processors
- Validators for oAuth, etc
- Finish processor data docs
- Implement collections
- Add validation of var type result. This can be declared in $this->required
- Add unit tests (Phing or Jenkins?)
- New Relic or Datadog
- Swagger.io 
-- reformat YAML so get delete put post is direct child if noun/verb 
-- Add description for each call to DB and meta
-- Add description to output
-- Maybe don't bother separating noun and very, just give URL
-- Maybe add summary
-- Change Process to Parameters

- Processors
-- Should non sysadmins be able to create processors without validation?
-- Change endpoint Url so that it converts response body into standard array format
-- Change resourceJson so that it accepts JSON in the body too
-- Change resourceYaml so that it accepts YAML in the body too
-- Email processor needs completing
-- Fix LoginStoreDrupal - how will this be used for validation?

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
