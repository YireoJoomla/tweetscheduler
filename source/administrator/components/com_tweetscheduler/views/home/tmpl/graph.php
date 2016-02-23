<?php
/*
 * Joomla! TweetScheduler
 *
 * @author Yireo (http://www.yireo.com/)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="well">
<div class="chart-container">
    <div id="chart"></div>
</div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($this->graphdata); ?>);
        var options = {
            title: 'Scheduled tweets for upcoming <?php echo $this->graphdays ?> days',
            legend: {position: 'none'}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart'));
        chart.draw(data, options);
      }
</script>
