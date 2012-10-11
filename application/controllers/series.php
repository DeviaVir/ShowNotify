<?php
class Series_Controller extends Base_Controller {	
	public $restful = true;
	public $layout  = 'layouts.main';
	private static $api    = 'C99DD2D05658653B';

	public function get_index( $arg = '' ) {
		if( Session::has( 'user' ) ) {
			$user = Session::get( 'user' );
			if( $arg ) {
				$serieURL = 'http://www.thetvdb.com/api/' . self::$api . '/series/' . $arg;
				$xml = simplexml_load_file( $serieURL );

				$serie = $xml->Series;

				$notifications = DB::table( 'users_notifications' )
					->where( 'user', '=', $user[ 'email' ] )
					->where( 'serie', '=', $arg );
				$notificationsData = $notifications->get();
				$this->layout->nest( 'content', 'home.serie', array(
					'serie' => $serie,
					'notifications' => ( $notifications->count() > 0 ? true : false ),
					'notificationsData' => ( $notificationsData ? (array)$notificationsData[0] : 0 )
				) );
			} else {
				$this->layout->nest( 'content', 'home.series', array( 'series' => 0, 'user' => Session::get( 'user' ) ) );
			}
		} else {
			$this->layout->nest( 'content', 'home.login', array( 'error' => false, 'message' => '' ) );
		}
	}
	
	public function post_index( $arg = '' ) {
		if( Input::get( 'search_submit' ) ) {
			if( Session::has( 'user' ) ) {
				$seriesURL = 'http://www.thetvdb.com/api/GetSeries.php?seriesname=' . Input::get( 'search' );
				$xml = simplexml_load_file( $seriesURL );

				$series = Array();
				$c      = 0;
				foreach( $xml as $serie ) {
					$c++;

					$jsonurl = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=" . urlencode( $serie->SeriesName );
					$result = json_decode( file_get_contents( $jsonurl ), true );

					$id   = (array)$serie->id;
					$name = (array)$serie->SeriesName;

					$series[( $c )] = Array(
						'id' => $id[0],
						'name' => $name[0],
						'image' => $result['responseData']['results'][0]['tbUrl']
					);
				}
				$this->layout->nest( 'content', 'home.series', array(
					'series' => $series
				) );
			} else {
				$this->layout->nest( 'content', 'home.login', array( 'error' => false, 'message' => '' ) );
			}
		} else if( Input::get( 'notify_submit' ) ) {
			if( Session::has( 'user' ) ) {
				$user = Session::get( 'user' );
				if( $arg ) {
					$serieURL = 'http://www.thetvdb.com/api/' . self::$api . '/series/' . $arg;
					$xml = simplexml_load_file( $serieURL );

					$serie = $xml->Series;

					$notifications = DB::table( 'users_notifications' )
						->where( 'user', '=', $user[ 'email' ] )
						->where( 'serie', '=', $arg );

					$notificationsData = array();
					if( $notifications->count() ) {
						$notifications->delete();
						$notifications = 0;
					} else {
						$notificationsData = array(
							'user' => $user[ 'email' ],
							'serie' => $arg,
							'name' => $serie->SeriesName,
							'after' => ( Input::get( 'after' ) ? 1 : 0 )
						);

						DB::table( 'users_notifications' )->insert( $notificationsData );
						$notifications = 1;
					}
					$this->layout->nest( 'content', 'home.serie', array(
						'serie' => $serie,
						'notifications' => ( $notifications > 0 ? true : false ),
						'notificationsData' => $notificationsData
					) );
				}
			} else {
				$this->layout->nest( 'content', 'home.login', array( 'error' => false, 'message' => '' ) );
			}
		}
	}
}
?>