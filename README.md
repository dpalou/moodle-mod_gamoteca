# Gamoteca #
Gamoteca is a human-connected, multi-player, creator platform and learner app to make learning fun, interactive and more human. 

Access the Creator platform at: https://www.gamoteca.com/ and download the Learner app from the Apple App or Google Play stores.

# Gamoteca - Moodle / Totara (LMS) integration #

This plugin allows course creatorsadmin users to addcreate a new activity within a course which provides a progress-trackable link to a learning experience (game) on the Gamoteca website / mobile application.

It will also includes a web service which will be allowed accesssed to Gamoteca to send user data i.e. game progress ( Not-started/In-Progress/Completed ), Score, time spent, etc. back to the LMS.

The link to the Gamoteca game will include the following data: Module ID User ID Site Code (to identify the Moodle site that the link is coming from)

Read more about how the integration works for Creators: https://intercom.help/gamoteca/en/articles/4770544-gamoteca-s-lms-integration 

## Webservice ##

The following webservice enablesenable's learnersusers progressgame data to be sent from Gamoteca to the LMS:

The endpoint to this Web service is: /webservice/rest/server.php?wstoken=[TOKEN]&wsfunction=gamoteca

The required parameter is 'games' which should be an array of arrays. The required keys in the child arrays for games are: courseid, gameid, userid, score, status and timespent.

* games[0][courseid] - courseid should be numeric - [COURSE ID]
* games[0][gameid] - gameid should be numeric - [COURSE MODULE ID]
* games[0][userid] - userid should be numeric - [USER ID]
* games[0][score] - score should be numeric
* games[0][status] - status should be string
* games[0][timespent] - timespent should be string


Webservice [TOKEN] needs to be generated and securely shared with Gamoteca.

## O-Auth DEPENDENCY ##

The Gamoteca plugin requires the O-Auth plugin to enable the learner to login to Gamoteca using their Moodle/Totara credentials. This is a requirement to authenticate the user via the web service and for the progress tracking to work.

local/oauth (https://github.com/projectestac/moodle-local_oauth) plugin for authenticating users via Gamoteca.

Add the plugin and go to: /local/oauth/index.php

Click on 'Add new client'

On the 'OAuth provider' screen set the following:
* Client identifier: gamoteca
* Redirect URL: [GAMOTECA URL]

## License ##

The plugin is maintained by Technovatio Limited (Gamoteca), UK. 
Moodle mobile compatibility was added by the International Training Centre of the ILO (ITCILO) in 2021.

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