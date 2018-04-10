# Changelog

## Version 0.3.0
* BC Break: changed ReferenceNamer interface `getPrefix` into `getReference` to allow non-numeric keys in namers 
* Add compatibility for newer versions of required libraries

## Version 0.2.0
* Enable strict type checking in all cases in ValueVisitor. Add option to disable it. (reported by nedlukies)

## Version 0.1.1
* Avoid creating new object instances without constructor args (chalasr)
* Do not ignore alternative generation strategies for identifiers (spinx)

## Version 0.1.0
* Initial version
* Support all baseline functionality