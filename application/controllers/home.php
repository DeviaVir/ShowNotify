<?php

class Home_Controller extends Base_Controller {

	/*
	|--------------------------------------------------------------------------
	| The Default Controller
	|--------------------------------------------------------------------------
	|
	| Instead of using RESTful routes and anonymous functions, you might wish
	| to use controllers to organize your application API. You'll love them.
	|
	| This controller responds to URIs beginning with "home", and it also
	| serves as the default controller for the application, meaning it
	| handles requests to the root of the application.
	|
	| You can respond to GET requests to "/home/profile" like so:
	|
	|		public function action_profile()
	|		{
	|			return "This is your profile!";
	|		}
	|
	| Any extra segments are passed to the method as parameters:
	|
	|		public function action_profile($id)
	|		{
	|			return "This is the profile for user {$id}.";
	|		}
	|
	*/
	
	public $restful = true;
	public $layout = 'layouts.main';

	public function get_index() {
		if( Session::has( 'user' ) ) {
			$user   = Session::get( 'user' );
			$series = DB::table( 'users_notifications' )->where( 'user', '=', $user[ 'email' ] )->order_by( 'name', 'asc' )->get();
			$this->layout->nest( 'content', 'home.index', array( 'user' => Session::get( 'user' ), 'series' => $series ) );
		} else {
			$this->layout->nest( 'content', 'home.login', array( 'error' => false, 'message' => '' ) );
		}
	}

	private function generatePassword( $length = 8 ) {
		// start with a blank password
		$password = "";

		// define possible characters - any character in this string can be
		// picked for use in the password, so if you want to put vowels back in
		// or add special characters such as exclamation marks, this is where
		// you should do it
		$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

		// we refer to the length of $possible a few times, so let's grab it now
		$maxlength = strlen($possible);

		// check for length overflow and truncate if necessary
		if ($length > $maxlength) {
			$length = $maxlength;
		}

		// set up a counter for how many characters are in the password so far
		$i = 0; 

		// add random characters to $password until $length is reached
		while ($i < $length) { 

			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);

			// have we already used this character in $password?
			if (!strstr($password, $char)) { 
				// no, so it's OK to add it onto the end of whatever we've already got...
				$password .= $char;
				// ... and increase the counter by one
				$i++;
			}

		}

		// done!
		return $password;

	}

	public function post_index() {
		if( Input::get( 'register_submit' ) ) {
			$user = DB::table( 'users' )->where( 'email', '=', Input::get( 'email' ) );
			$userGet = $user->get();
			$error = null;
			if( !Input::get( 'email' ) ) {
				$error = 'You did not enter an e-mail address.';
			} else if( !filter_var( Input::get( 'email' ), FILTER_VALIDATE_EMAIL ) ) {
				$error = 'Your e-mail address does not look like an e-mail address.. Are you sure this is your e-mail address?';
			} else if( $user->count() > 0 && $userGet[0]->paid ) {
				$error = 'This e-mail address has already been registered! Your password should be in your mailbox somewhere... Mail chase@sillevis.net if you can\'t find it.';
			}

			if( is_Null( $error ) ) {
				$pass = self::generatePassword( 8 );
				$password = hash( 'sha512', ( sha1( strtolower( Input::get( 'email' ) ) . $pass ) ) );
				DB::table( 'users' )->insert( array(
					'email' => Input::get( 'email' ),
					'password' => $password,
					'paid' => 1
				));

				$headers =  'From: activate@shownotify.nl' . "\r\n" .
							'Reply-To: chase@sillevis.net' . "\r\n" .
							'X-Mailer: PHP/' . phpversion() . "\r\n" .
							'MIME-Version: 1.0' . "\r\n" . 
							'Content-type: text/html; charset=iso-8859-1';
				mail( Input::get( 'email' ), 'Thank you from ShowNotify', 'Hey! We have just received your registration, thanks for that!
					<br /><br />
					You now have access to your ShowNotify account with the following details, e-mail address: <strong>' . Input::get( 'email' ) . '</strong>, password: <strong>' . $pass . '</strong>
					<br /><br />
					If you run into any problems, please shoot a mail at <a href="mailto:chase@sillevis.net" target="_blank" title="chase@sillevis.net">me</a>.
					<br /><br />
					- Chase Sillevis<br />
					https://chase.sillevis.net/', $headers );
				$this->layout->nest( 'content', 'home.response' , array( 'email' => Input::get( 'email' ) ) );
			} else {
				$this->layout->nest( 'content', 'home.login', array(
					'error' => true,
					'message' => $error
				) );
			}
		} else if( Input::get( 'login_submit' ) ) {
			// Check if credentials are OK
			$user = DB::table( 'users' )
				->where( 'email', '=', Input::get( 'email' ) )
				->where( 'password', '=', hash( 'sha512', ( sha1( strtolower( Input::get( 'email' ) ) . Input::get( 'password' ) ) ) ) );
			$userGet = $user->get();
			$error = null;
			if( !Input::get( 'email' ) ) {
				$error = 'You did not enter an e-mail address.';
			} else if( !Input::get( 'password' ) ) {
				$error = 'You did not enter a password.';
			} else if( !filter_var( Input::get( 'email' ), FILTER_VALIDATE_EMAIL ) ) {
				$error = 'Your e-mail address does not look like an e-mail address.. Are you sure this is your e-mail address?';
			} else if( !$user->count() ) {
				$error = 'Your e-mail address or password were not recognized.';
			}/* else if( !$userGet[0]->paid ) {
				$error = 'You haven\'t made the 1 euro payment yet, <a href="http://gum.co/ShowNotify" title="Activate">activate</a> your account now</a>';
			}*/

			if( is_Null( $error ) ) {
				// Set user session
				$userData = Array(
					'email' => $userGet[0]->email
				);
				Session::put( 'user', $userData );

				$user   = Session::get( 'user' );
				$series = DB::table( 'users_notifications' )->where( 'user', '=', $user[ 'email' ] )->order_by( 'name', 'asc' )->get();
				$this->layout->nest( 'content', 'home.index', array( 'user' => Session::get( 'user' ), 'series' => $series ) );
			} else {
				$this->layout->nest( 'content', 'home.login', array(
					'error' => true,
					'message' => $error
				) );
			}
		} else if( Input::get( 'search_submit' ) ) {
			if( Session::has( 'user' ) ) {
				$seriesURL = 'http://www.thetvdb.com/api/GetSeries.php?seriesname=' . Input::get( 'search' );
				$xml = simplexml_load_file( $seriesURL );

				$series = $xml;
				//print '<pre>';print_r( $series );print '</pre>';
				$this->layout->nest( 'content', 'home.series', array(
					'series' => $series
				) );
			} else {
				$this->layout->nest( 'content', 'home.login', array( 'error' => false, 'message' => '' ) );
			}
		}
	}
}