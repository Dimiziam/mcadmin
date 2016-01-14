<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>


<div id="container2" style="height: 400px; min-width: 310px"></div>

<br><br>
<div id="container" style="height: 400px; min-width: 310px"></div>

<br><br>

<div id="container1" style="height: 400px; min-width: 310px"></div>

<script>


	$(function () {

		var seriesOptions = [],
			seriesCounter = 0,
			names = ['PayPal','PayU','Total'],
			urls = ['/report/AjaxGetPaypalTotal', '/report/AjaxGetPayUTotal','/report/AjaxGetTotalPayments'];



		Highcharts.setOptions({
			global: {
				timezoneOffset: - 2 * 60
			}
		});

		/**
		 * Create the chart when all data is loaded
		 * @returns {undefined}
		 */
		function createChart() {

			$('#container2').highcharts('StockChart', {

				rangeSelector: {
					selected: 4
				},

				xAxis: {
					type: 'datetime'
				},

				yAxis: {
//					labels: {
//						formatter: function () {
//							return (this.value > 0 ? ' + ' : '') + this.value + '%';
//						}
//					},

					plotLines: [{
						value: 0,
						width: 2,
						color: 'silver'
					}]
				},

//				plotOptions: {
//					series: {
//						compare: 'percent'
//					}
//				},

				tooltip: {
					pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
					valueDecimals: 2
				},

				series: seriesOptions
			});
		}

		$.each(urls, function (i, url) {

			$.getJSON(url, function (data) {

				seriesOptions[i] = {
					name: names[i],
					data: data
				};

				// As we're loading the data asynchronously, we don't know what order it will arrive. So
				// we keep a counter and create the chart when all the data is loaded.
				seriesCounter += 1;

				if (seriesCounter === urls.length) {
					createChart();
				}

			});
		});

	});
	

</script>