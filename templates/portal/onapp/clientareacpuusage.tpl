<link href="modules/servers/onapp/includes/onapp.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="{$smarty.const.ONAPP_FILE_NAME}?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <strong>{$LANG.onappcpuusage}</strong>
    | <a title="{$LANG.onappipaddresses}" href="{$smarty.const.ONAPP_FILE_NAME}?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="{$smarty.const.ONAPP_FILE_NAME}?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="{$smarty.const.ONAPP_FILE_NAME}?page=backups&id={$id}">{$LANG.onappbackups}</a>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="{$smarty.const.ONAPP_FILE_NAME}?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
</div>
<p>{$LANG.onappcpuusagetitle}</p>
<br/>
<script type="text/javascript" src="{$address}/javascripts/Highcharts-2.1.9/js/highcharts.js"></script>
      <div id="chart7c402aad61"></div>
      {literal}
      <script type="text/javascript">
              //<![CDATA[
          new Highcharts.Chart({series: {/literal}{$data}{literal}}], title: {text: '{/literal}{$LANG.onapphourly}{literal}', x: -20}, credits: {enabled: false}, chart: {height: 300, renderTo: 'chart7c402aad61', width: 680, defaultSeriesType: 'line', zoomType: 'x'}, tooltip: {shared: true, crosshairs: true}, xAxis: {type: 'datetime', labels: {formatter: function() { return Highcharts.dateFormat("%e %b %H:%M", this.value); }}}, yAxis: {title: {text: null}}, plotOptions: {series: {marker: {states: {hover: {enabled: true}}, enabled: false, lineWidth: 0}}}, lang: {decimalPoint: '.', thousandsSep: 3, downloadPNG: 'Download PNG image', weekdays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], downloadJPEG: 'Download JPEG image', resetZoomTitle: 'Reset zoom level 1:1', exportButtonTitle: 'Export to raster or vector image', resetZoom: 'Reset zoom', loading: 'Loading....', downloadPDF: 'Download PDF document', months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], printButtonTitle: 'Print the chart', downloadSVG: 'Download SVG vector image', shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']}});
        //]]
      </script>
      {/literal}
<br/>
