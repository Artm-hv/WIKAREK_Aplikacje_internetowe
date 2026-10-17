  <html>
  <head>
    <!-- <script src="https://www.gstatic.com/charts/loader.js"></script> -->
    <script src="skrypt.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
 
   function drawChart() {
     var z = '<?php 
                echo $_POST['dane_all']
              ?>';
     var z1 = JSON.parse(z);
     var data =  google.visualization.arrayToDataTable(z1);
     var options = {
	 hAxis: {
		maxAlternation: 1,
		slantedText: true,
		slantedTextAngle: 90
	        },
          title: 'Wykres',
          curveType: 'function',
		  legend: 'none',
		  pointShape: 'diamond',
		  pointSize: 10,
		  height: 850,
		  width: '90%'
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
     }
    </script>

  </head>
   <body>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
    <div id="curve_chart"></div>	  
  </body>
</html>