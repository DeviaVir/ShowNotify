<?php

class Activate_Controller extends Base_Controller {

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
	public $layout = 'layouts.empty';

	public function get_index() {
		self::post_index();
	}

	public function post_index() {
		if( Input::get( 'email' ) ) {
			$pass = self::generatePassword( 8 );
			$password = hash( 'sha512', ( sha1( strtolower( Input::get( 'email' ) ) . $pass ) ) );
			DB::table( 'users' )
				->where( 'email', '=', Input::get( 'email' ) )
				->update( array(
					'paid' => '1',
					'password' => $password
				) );

			$headers =  'From: activate@shownotify.nl' . "\r\n" .
						'Reply-To: chase@sillevis.net' . "\r\n" .
						'X-Mailer: PHP/' . phpversion() . "\r\n" .
						'MIME-Version: 1.0' . "\r\n" . 
						'Content-type: text/html; charset=iso-8859-1';
			mail( Input::get( 'email' ), 'Thank you from ShowNotify', 'Hey! We have just received your payment, thanks for that!
				<br /><br />
				You have now activated your account and you can log in with your e-mail address <strong>' . Input::get( 'email' ) . '</strong> and this password: <strong>' . $pass . '</strong>
				<br /><br />
				If you run into any problems, please shoot a mail at <a href="mailto:chase@sillevis.net" target="_blank" title="chase@sillevis.net">me</a>.
				<br /><br />
				- Chase Sillevis<br />
				https://twitter.com/deviavir<br />
				https://chase.sillevis.net/', $headers );
			$this->layout->nest( 'content', 'home.response' , array() );
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
}

?>