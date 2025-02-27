<nav {{ $attributes }}  aria-label="breadcrumb">
	<ol class="breadcrumb">
		@foreach ($items as $link => $item)
			<li class="breadcrumb-item">
				@if ($item["link"] == null)
					{{ $item["name"] }}
				@else
					<a href="{{ url($item["link"]) }}">{{ $item["name"] }}</a>
				@endif
			</li>
		@endforeach
	</ol>
</nav>

