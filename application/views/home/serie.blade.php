<div class="serie">
	<h1>{{ $serie->SeriesName }}</h1>
	<p>
		This show first aired (or will air) {{ $serie->FirstAired }}, and has a rating of {{ $serie->Rating }}. Please find an overview below.
		@if( $serie->IMDB_ID )
			<br /><br />
			<a href="http://www.imdb.com/title/{{ $serie->IMDB_ID }}" title="IMDB" class="btn imdb" target="_blank">IMDB info</a>
			<br /><br />
		@endif
		{{ $serie->Overview }}
	</p>
	<form class="forms" method="post">
		<fieldset>
			<input type="hidden" name="seriesid" value="{{ $serie->id }}" />
			<ul>
				<li>
					<label for="after"><input type="checkbox" name="after" id="after" value="1"
						@if( $notificationsData && $notificationsData[ 'after' ] )
							checked="checked"
						@endif
					/> Receive one day after the show aired?</label>
				</li>
				<li>
					@if( $notifications )
						<input type="submit" class="btn input-error" name="notify_submit" id="notify_submit" value="Disable notifications" />
					@else
						<input type="submit" class="btn input-success" name="notify_submit" id="notify_submit" value="Get notifications" />
					@endif
				</li>
		</fieldset>
	</form>
	<a href="{{ URL::to( '/series/index' ) }}" title="Search" class="no-decoration">&lt; Search</a>
</div>