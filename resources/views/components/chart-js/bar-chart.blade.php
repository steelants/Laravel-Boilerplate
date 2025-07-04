<div>
	<canvas height=500 id="line-chart-{{ $uuid }}"></canvas>
	<script>
		var labels = @json($labels);

		new Chart(document.getElementById("line-chart-{{ $uuid }}"), {
			type: 'bar',
			data: {
				labels: @json($labels),
				datasets: @json($datasets)
			},
			options: {
				maintainAspectRatio: false,
				responsive: true,
				animation: false,
				plugins: {
					legend: {
						display: false
					},
					tooltips: {
						callbacks: {
							label: function(tooltipItem) {
								return labels[tooltipItem.index];
							}
						}
					}
				}
			}
		});
	</script>
</div>
