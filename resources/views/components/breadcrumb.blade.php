<nav {{ $attributes }} aria-label="breadcrumb">
	<ol class ="breadcrumb">
		@foreach ($items as $item)
			<li class="breadcrumb-item">
				<a href={{ $item['link'] }}">{{ $item['name'] }}</a>
			</li>
		@endforeach
	</ol>
</nav>
