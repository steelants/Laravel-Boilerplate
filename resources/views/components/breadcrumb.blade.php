<nav {{ $attributes }} aria-label="breadcrumb" x-data="{
    options: @js(array_values($items))
}">
	< ol class ="breadcrumb">
		<template :key="option.link" x-for="option in options">

			<li class="breadcrumb-item">
				@if ($item["link"] == null)
					{{ $item["name"] }}
				@else
					<a href="{{  }}" x-text="option.name"></a>
				@endif
			</li>

		</template>
		</ol>
</nav>
