<tr>
	@foreach( $presenter->columns() as $column )
		<td>
			{{ $presenter->formatValue( $item, $column ) }}
		</td>
	@endforeach
</tr>
