<?php

// The displayed name of the server
$SERVER_NAME = 'my Plex server';

// The actual URL of the server
$SERVER_URL = $_SERVER['SERVER_NAME'];

// The URL to ping
$PLEX_SERVER = $SERVER_URL.':32400/web';

// PlexRequests URL
$PLEX_REQUESTS = $SERVER_URL.':3000/search';

// Plex App URL
// $PLEX_URL = 'app.plex.tv/web/app';
$PLEX_URL = $PLEX_SERVER;


//Slack Team URL
$SLACK_URL = "" ;
//Set to true to show a desaturated logo
$SERVER_STATS_DESATURATE = false ;


//Server Stats URL
$SERVER_STATS_URL = "" ;
//Set to true to show a desaturated logo
$SLACK_DESATURATE = false ;


// DONATE (leave both blank to hide Donate section)

// Donate URL
$DONATE_URL = '';
// PayPal inner-most "hosted_button_id" value
$PAYPAL_BUTTON_ID = '';


// LIBRARY

// Minimum number of movies in your library
$MOVIE_COUNT = 100;
// Minimum number of TV shows in your library
$TV_COUNT = 30;

// PlexPy URL
$PLEXPY_URL = $SERVER_URL.':8181';
// PlexPy API Key
$PLEXPY_API = '';
// Comma-separated list of section names to count as Movies
$MOVIE_LIBS = 'Movies';
// Comma-separated list of section names to count as TV Shows
$TV_LIBS = 'TV Shows';

// TV SHOW CALENDAR
// For more info see https://fullcalendar.io/docs/google_calendar/
$GOOGLE_CALENDAR_API_KEY = '';

$GOOGLE_CALENDAR_ID = '';


?>
