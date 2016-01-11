<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>

<div id="container" style="height: 400px; min-width: 310px"></div>

<br><br>

<div id="container1" style="height: 400px; min-width: 310px"></div>

<script>


	$(function () {

		//$.getJSON('https://www.highcharts.com/samples/data/jsonp.php?filename=aapl-c.json&callback=?', function (data) {
		$.getJSON('/report/AjaxGetPaypalTotal', function (data) {

			// Create the chart
			$('#container').highcharts('StockChart', {

				global: {
					useUTC: false
				},

				rangeSelector : {
					selected : 1
				},

				title : {
					text : 'Amount PayPal'
				},

				series : [{
					name : 'Paypal',
					data : data,
					tooltip: {
						valueDecimals: 2
					}
				}]
			});
		});

		$.getJSON('/report/AjaxGetPayUTotal', function (data) {

			// Create the chart
			$('#container1').highcharts('StockChart', {

				global: {
					useUTC: false
				},

				rangeSelector : {
					selected : 1
				},

				title : {
					text : 'Amount PayuU'
				},

				series : [{
					name : 'PayU',
					data : data,
					tooltip: {
						valueDecimals: 2
					}
				}]
			});
		});

	});






	

</script>