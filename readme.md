# ShowNotify
This PHP (using the Laraval framework) project allows users to be notified for new shows.

## Instructions
Read about how to use and develop against ShowNotify below. Pay special attention to e-mail addresses when forking, I have stated my own for a lot of reply-to's.

### Styles
The styles are based on my [kube-framework boilerplate](https://github.com/DeviaVir/boilerplate-kube), find usage instructions there.

### Cron
0 10 * * * shownotify.nl/public/cron/index

Find the controller that belongs in /application/controllers/cron.php below, please request an API key from thetvdb.com

### Hosting
I have hosted ShowNotify at [dualdev.com](https://dualdev.com/), with the following nginx config:

```
        location /public {
            index index.php;
            if (!-e $request_filename) {
                rewrite  ^(.*)$  /public/index.php?/$1 last;
                break;
            }
        }
        
        location / {
            rewrite ^ /public permanent;
        }
```

### Database
Create a new database file based off of default Laravel database settings. It should be placed here: application/config/database.php
Find the structure I used for my database all the way below!

## License
ShowNotify is open-sourced software licensed under the MIT License.

## MySQL

```
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `paid` tinyint(1) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS `users_notifications` (
  `user` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `after` tinyint(1) NOT NULL default '0',
  `name` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

```

## /application/controllers/cron.php

```
<?php
class Cron_Controller extends Base_Controller {	
	public $restful = true;
	public $layout  = 'layouts.main';
	private static $api    = /* Your API key from thetvdb.com here */'';

	public function get_index() {
		if( $_SERVER[ 'REMOTE_ADDR' ] == '95.211.76.103'
		 || $_SERVER[ 'REMOTE_ADDR' ] == '2001:1af8:4500:a005:6::dd:c' ) { // DualDev cron servers
			set_time_limit ( 300 );
			$tomorrow = date( 'Y-m-d', ( time() + 86400 ) );
			$yesterday = date( 'Y-m-d', ( time() - 86400 ) );

			$groups = DB::Query( 'SELECT * FROM `users_notifications` GROUP BY `serie`' );
			foreach( $groups as $group ) {
				$serieURL = 'http://www.thetvdb.com/api/' . self::$api . '/series/' . $group->serie;
				$serie = simplexml_load_file( $serieURL );

				# Add to queue?

				$episodeURL = 'http://www.thetvdb.com/api/GetEpisodeByAirDate.php?apikey=' . self::$api . '&seriesid=' . $group->serie . '&airdate=' . $tomorrow;
				$episode = simplexml_load_file( $episodeURL );

				$error = $episode->Error;
				if( reset( $error ) != '' ) {
					// Continue
					print 'Skip: ' . $group->serie . '<br />';
				} else {
					$users = DB::table( 'users_notifications' )->where( 'serie', '=', $group->serie )->where( 'after', '=', 0 );
					foreach( $users->get() as $user ) {
						$headers =  'From: notifications@shownotify.nl' . "\r\n" .
							'Reply-To: chase@sillevis.net' . "\r\n" .
							'X-Mailer: PHP/' . phpversion() . "\r\n" .
							'MIME-Version: 1.0' . "\r\n" . 
							'Content-type: text/html; charset=iso-8859-1';
						mail( $user->user, 'Notification for tomorrow - ShowNotify', 'Hey! Look sharp, your show <strong>' . $serie->Series->SeriesName . '</strong> will continue tomorrow, it will be episode <strong>' . $episode->Episode->EpImgFlag . '</strong> of season <strong>' . $episode->Episode->SeasonNumber . '</strong> and will be called <strong>' . $episode->Episode->EpisodeName . '</strong>.<br /><br />
							Here is a short overview:<br />
							' . $episode->Episode->Overview . '
							<br /><br />
							Have fun watching and let me know how you liked it! :-)<br /><br />
							- Chase Sillevis<br />
							https://chase.sillevis.net/', $headers );
					}
				}

				$episodeURL = 'http://www.thetvdb.com/api/GetEpisodeByAirDate.php?apikey=' . self::$api . '&seriesid=' . $group->serie . '&airdate=' . $yesterday;
				$episode = simplexml_load_file( $episodeURL );

				$error = $episode->Error;
				if( reset( $error ) != '' ) {
					// Continue
					print 'Skip: ' . $group->serie . '<br />';
				} else {
					$users = DB::table( 'users_notifications' )->where( 'serie', '=', $group->serie )->where( 'after', '=', 1 );
					foreach( $users->get() as $user ) {
						$headers =  'From: notifications@shownotify.nl' . "\r\n" .
							'Reply-To: chase@sillevis.net' . "\r\n" .
							'X-Mailer: PHP/' . phpversion() . "\r\n" .
							'MIME-Version: 1.0' . "\r\n" . 
							'Content-type: text/html; charset=iso-8859-1';
					mail( $user->user, 'Notification from yesterday - ShowNotify', 'Hey! Look sharp, your show <strong>' . $serie->Series->SeriesName . '</strong> has continued, it was episode <strong>' . $episode->Episode->EpImgFlag . '</strong> of season <strong>' . $episode->Episode->SeasonNumber . '</strong> and has been called <strong>' . $episode->Episode->EpisodeName . '</strong>.<br /><br />
						Here is a short overview:<br />
						' . $episode->Episode->Overview . '
						<br /><br />
						Have fun watching and let me know how you liked it! :-)<br /><br />
						- Chase Sillevis<br />
						https://chase.sillevis.net/', $headers );
					}
				}
			}

			$this->layout->nest( 'content', 'home.cron', array() );
		} else {
			return 'access denied';
		}
	}
}
?>
```