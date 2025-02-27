<nav {{ $attributes }} aria-label="breadcrumb" x-data="{
    options: @js($items)
}">
	<ol class ="breadcrumb">
		<template :key="option.link" x-for="option in options">
			<li class="breadcrumb-item">
				<a x-bind:href="option.link" x-text="option.name"></a>
			</li>
		</template>
	</ol>
</nav>
