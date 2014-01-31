<div class="series">
	@if( $series )
		<h1>Search <span>("{{Input::get( 'search' )}}")</span></h1>
	@else
		<h1>Search</h1>
	@endif
	<form method="post" class="forms">
		<ul>
			<li>
				<label for="search" class="bold">TV Show</label>
				<input type="text" class="input-gray" name="search" id="search" value="{{ Input::get( 'search' ) }}" placeholder="The Walking Dead" />
			</li>
			<li>
				<input type="submit" name="search_submit" id="search_submit" class="btn" value="Search" />
			</li>	
		</ul>
	</form>
	@if( $series )
	<h3>Search results</h3>
	<ul class="shows">
	@foreach( $series as $serie )
		<li onclick="window.location='/series/index/{{ $serie[ 'id' ] }}';">
			<span class="img">
				<img src="{{ $serie[ 'image' ] }}" height="140" alt="{{ $serie[ 'name' ] }}" />
			</span>
			<a href="/series/index/{{ $serie[ 'id' ] }}" title="{{ $serie[ 'name' ] }}">{{ $serie[ 'name' ] }}</a>
		</li>
	@endforeach
	</ul>
	@endif
</div>