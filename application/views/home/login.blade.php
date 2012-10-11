@if ( $error )
	<article class="error">
		<p>{{ $message }}</p>
	</article>
@endif
<div class="main">
	<h1>Welcome, traveler!</h1>
	<p>
		So you like watching TV shows? Great! So do I. One thing that annoyed me was that I never really knew when the show I liked would air (or had already aired), so I decided to do something about that.<br /><br />
		Say hi to ShowNotify. Simply enter your e-mail address and desired password in the Sign up form below, we will send you a password which you can use to log in. Simply find your password in your mailbox if you want to add shows later! After logging in, you simply search for the series you like and add them to your watchlist.<br /><br />
		Sounds simple right? Give it a try! It's <strong>free</strong> :-)
	</p>
</div>
<div class="forms">
	<form class="forms" method="post">
		<fieldset class="liner">
			<legend>
				<span>Access</span>
			</legend>
			<ul>
				<li>
					<label for="email" class="bold">
						E-mail
					</label>
					<input type="email" class="gray-input" name="email" id="email" placeholder="chase@shownotify.nl" />
				</li>
				<li>
					<label for="password" class="bold">
						Password
					</label>
					<input type="password" class="gray-input" name="password" id="password" placeholder="*********" />
				</li>
				<li>
					<input type="submit" name="login_submit" id="login_submit" class="btn" value="Enter" />
				</li>
			</ul>
		</fieldset>
	</form>
	<form class="forms" method="post">
		<fieldset class="liner">
			<legend>
				<span>Sign up</span>
			</legend>
			<ul>
				<li>
					<label for="email" class="bold">
						E-mail
					</label>
					<input type="email" class="gray-input" name="email" id="email" placeholder="chase@shownotify.nl" />
				</li>
				<li>
					<input type="submit" name="register_submit" id="register_submit" class="btn" value="Join" />
				</li>
			</ul>
		</fieldset>
	</form>
</div>