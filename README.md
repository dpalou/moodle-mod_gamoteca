# Gamoteca #
Gamoteca is a platform to easily create human-to-human, collaborative learning experiences for individuals to learn and practice alongside other learners at scale, whenever they want wherever they want.

Access the Creator platform at: https://www.gamoteca.com/ and download the Learner app from the [Apple App](https://apps.apple.com/gb/app/gamoteca/id1238297749) or [Google Play](https://play.google.com/store/apps/details?id=com.gamoteca.app&hl=en&gl=US) stores.

# Gamoteca - Moodle integration #

This plugin allows course creators to add a new Gamoteca Technology Enhanced Collaboration Activity (TECA) within a course which provides a progress-trackable link to a TECA on the Gamoteca website / mobile app.

It also includes a web service which allows Gamoteca to send user data i.e. game progress (Not-started/In-Progress/Completed ), Score, time spent, etc. back to the LMS.

The link to Gamoteca will include the following data:
- Module ID
- User ID
- Site Code (to identify the Moodle site that the link is coming from)

Read more about how the integration works for Gamoteca Creators:
https://intercom.help/gamoteca/en/articles/4770544-gamoteca-s-lms-integration

Note: The Moodle plugin requires a Gamoteca Pro or Enterprise license for integration with Moodle.

## Encryption Key Setting ##
Once the plugin is installed, a plugin-level setting key in Moodle is used to encrypt user details passed from the LMS to Gamoteca, and ensures the user and course information are synced up between the two platforms. The setting is available on Moodle under (Site Administration / Plugins / Gamoteca).

The encryption key to be entered above is available for Gamoteca team administrators under (Manage Teams / Integrations).

Dependency: The encryption key requires the PHP-Sodium extension. Read more here:
https://docs.moodle.org/311/en/Environment_-_PHP_extension_sodium

## Webservice ##

The following webservice enables learners progress to be sent from Gamoteca to the LMS asynchronously:

The endpoint to this Web service is: /webservice/rest/server.php?wstoken=[TOKEN]&wsfunction=gamoteca

The required parameter is 'games' which should be an array of arrays. The required keys in the child arrays for games are: courseid, gameid, userid, score, status and timespent.

* games[0][courseid] - courseid should be numeric - [COURSE ID]
* games[0][gameid] - gameid should be numeric - [COURSE MODULE ID]
* games[0][userid] - userid should be numeric - [USER ID]
* games[0][score] - score should be numeric
* games[0][status] - status should be string
* games[0][timespent] - timespent should be string


A Webservice [TOKEN] needs to be generated on Moodle under (Site Administration/ Web services / Manage tokens) and securely shared with Gamoteca.

## License ##

The plugin is maintained by Technovatio Limited (Gamoteca), UK.

The plugin was originally developed in 2020 by Catalyst IT Europe (http://www.catalyst-eu.net/)

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see [http://www.gnu.org/licenses/].