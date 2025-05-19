<div>
    @php($graph = Str::random(6))
    <canvas height="300" id="chart-{{ $graph }}"></canvas>
    <script>
        { // scope
            var ctx = document.getElementById('chart-{{ $graph }}').getContext('2d');
            var PieChart = new Chart(ctx, {
                type: 'pie',
                height: 300,
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: {!! json_encode($datasets) !!},
                },
                options: {!! json_encode($options) !!}
            });
        }
    </script>
</div>
