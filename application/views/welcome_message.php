<?php
/* Include the `../src/fusioncharts.php` file that contains functions to embed the charts.*/
include("fusioncharts/fusioncharts.php");
?>
<html>

<head>
	<!-- FusionCharts Library -->
	<script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
	<script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
	<script>
		updateData = function() {
			var value = document.getElementById("dial-val").value;
			FusionCharts("angulargauge-1").setDataForId("dial1", value);
		}
	</script>
</head>

<body>
	<?php
	$gaugeData = [
		'chart' =>
		array(
			'caption' => 'Customer Satisfaction Score',
			'subcaption' => 'Los Angeles Topanga',
			'plotToolText' => 'Current Score: $value',
			'theme' => 'fusion',
			'chartBottomMargin' => '50',
			'showValue' => '1',
		),
		'colorRange' =>
		array(
			'color' =>
			array(
				[
					'minValue' => '0',
					'maxValue' => '45',
					'code' => '#e44a00',
				],
				[
					'minValue' => '45',
					'maxValue' => '75',
					'code' => '#f8bd19',
				],
				[
					'minValue' => '75',
					'maxValue' => '100',
					'code' => '#6baa01',
				],
			),
		),
		'dials' =>
		array(
			'dial' =>
			array(
				[
					'value' => '69',
					'id' => 'dial1',
				],
			),
		),
	];

	$jsonEncodedData = json_encode($gaugeData);

	// chart object
	$Chart = new FusionCharts("angulargauge", "angulargauge-1", "450", "250", "angulargauge-container", "json", $jsonEncodedData);
	// Render the chart
	$Chart->render();
	?>
	<h3>Update data at runtime</h3>
	<div id="angulargauge-container">Chart will render here!</div>
	<div>
		<label for="dial-val">Input dial value</label>
		<input name="dial-val" id="dial-val" type="number" />
		<input type="button" name="update dial" value="Update Dial" onclick="updateData()" />
	</div>
</body>

</html>