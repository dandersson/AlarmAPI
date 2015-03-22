AlarmAPI
========
By installing a small embedded system to log the state of the pub alarm to a database, this state is presented via this [RESTful](http://en.wikipedia.org/wiki/Representational_state_transfer) API in a JSON structure.

Currently implemented in this repository is a PHP-based API call to get the current alarm state and the time it was set, along with localized strings describing the state.

Also included is a sample JavaScript library for making AJAX calls to this API for periodically updating a status display. Developed simultaneously was a JavaScript library for performing such AJAX calls to a certain API with a JSON structure payload and applying a given callback function to the JSON-parsed result. This was split into its own repository, available at [dandersson/APIRequest](https://github.com/dandersson/APIRequest).

The project is available at [dandersson/AlarmAPI](https://github.com/dandersson/AlarmAPI).

Dependencies
------------
* [APIRequest](https://github.com/dandersson/APIRequest)

Sample implementation
---------------------
A minimal implementation of this API is included in `index.html` in the root of the repository.

Minification
------------
Minification of CSS and JavaScript depends on [YUI Compressor](http://yui.github.io/yuicompressor/) and [Google Closure Compiler](https://developers.google.com/closure/compiler/) and can be automatically applied using e.g. [GNU Make](https://www.gnu.org/software/make/) through the included makefile.

Software license
----------------
[Apache license, version 2.0](https://www.apache.org/licenses/LICENSE-2.0).
