<?php
/*
 * Joomla! TweetScheduler
 *
 * @author Yireo (http://www.yireo.com/)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="chart-container">
    <h3>Post statistics of upcoming 30 days</h3>
    <div id="chart"></div>
</div>

<script>
var data = [<?php echo implode(', ', $this->graphdata); ?>];

jQuery.noConflict();
jQuery(function($) {
    $.jqplot('chart', [data], {
        axes:{
            xaxis:{
                renderer:$.jqplot.DateAxisRenderer,
                tickOptions:{formatString:'%#d'},
                tickInterval:'1 day'
            }
        },
        series:[{showMarker:false}],
    });
});
</script>
