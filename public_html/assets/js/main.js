var app = {
	load: function() {
		if( Modernizr.svg ) {
			document.getElementById('logo').className='svg';
		}
	}
};

function ready(cb) {
	document.addEventListener('DOMContentLoaded', function() {
		cb;
	});
	
   /in/.test(document.readyState) // in = loadINg
      ? setTimeout('ready('+cb+')', 9)
      : cb();
}
ready( function() {
	app.load();
});