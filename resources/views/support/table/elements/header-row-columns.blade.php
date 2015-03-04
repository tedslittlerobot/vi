<tr>
	@foreach( $presenter->columns() as $column )
		<th>
			@if( $presenter->isSortable( $column ) )
				<a href="{{ $presenter->sortingLink( $column ) }}">
					{{ $presenter->formatHeader($column) }}
				</a>
			@else
				{{ $presenter->formatHeader($column) }}
			@endif
		</th>
	@endforeach
</tr>
