<div>
    <canvas id="pie-chart-{{ $uuid  }}"></canvas>
	<script>
		var labels = @json($labels);

		new Chart(document.getElementById("pie-chart-{{ $uuid  }}"), {
            type: 'pie',
			data: {
                labels: @json($labels),
				datasets: [{
                    backgroundColor: @json($colors),
					data: @json($values),
				}]
			},
			options: {
                plugins:{

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
