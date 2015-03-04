<tbody>
	@foreach($presenter->items() as $item)
		@include('vi::support.table.row')
	@endforeach
</tbody>
