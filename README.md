# Gamoteca #

Gamoteca - Moodle / Totara (LMS) integration

This plugin allows admin users to create a new activity which provides a link
to a game on Gamoteca website / mobile application.

It will also include a web service which will be allowed accessed to Gamoteca to
send user data i.e. game progress ( Not-started/In-Progress/Completed ), Score,
time spent, etc. back to the LMS

The link to the Gamoteca game will include the following data:
Module ID
User ID
Site Code (to identify the Moodle that the link is coming from)

## WEBSERVICE ##

The following webservice enable's users game data to be sent from Gamoteca to the LMS:

The endpoint to this Web service is: /webservice/rest/server.php?wstoken=[TOKEN]&wsfunction=gamoteca

The required parameter is 'games' which should be an array of arrays. The required keys in the child arrays for games are: courseid, gameid, userid, score, status and timespent.

* games[0][courseid] - courseid should be numeric - [COURSE ID]
* games[0][gameid] - gameid should be numeric - [COURSE MODULE ID]
* games[0][userid] - userid should be numeric - [USER ID]
* games[0][score] - score should be numeric
* games[0][status] - status should be string
* games[0][timespent] - timespent should be string


Webservice [TOKEN] needs to be generated and securely shared with Gamoteca.

## DEPENDENCY ##

local/oauth (https://github.com/projectestac/moodle-local_oauth) plugin for authenticating users via Gamoteca.

Add the plugin and go to: /local/oauth/index.php

Click on 'Add new client'

On the 'OAuth provider' screen set the following:
* Client identifier: gamoteca
* Redirect URL: [GAMOTECA URL]

## License ##

2020 Catalyst IT Europe (http://www.catalyst-eu.net/)

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see [http://www.gnu.org/licenses/].