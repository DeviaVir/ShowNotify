<div class="series">
	<h1>Your shows</h1>
	@if( count( $series ) )
	<ul>
		@foreach( $series as $serie )
			<li>
				<a href="/series/index/{{ $serie->serie }}" title="{{ $serie->name }}">{{ $serie->name }}</a>
			</li>
		@endforeach
	</ul>
	@else
		<span class="error">You did not add any shows yet. Why not <a href="{{ URL::to( '/series/index' ) }}" title="Search">search</a> for some shows?</span>
	@endif
</div>