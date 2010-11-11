<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="onapp.php?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <strong>CPU Usage</strong>
    | <a title="{$LANG.onappipaddresses}" href="onapp.php?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="onapp.php?page=backups&id={$id}">{$LANG.onappbackups}</a>
</div>
<p>{$LANG.onappcpuusagetitle}</p>
<br/>
{if $xaxis != "" || $yaxis != ""}
<script type="text/javascript" src="modules/servers/onapp/includes/swfobject.js"></script>
<h2 class="heading2">Per Hour</h2>
      <div id="chart7c402aad61"><strong>You need to upgrade your Flash Player</strong></div>
      <script type="text/javascript">
      // <![CDATA[
      var so7c402aad61 = new SWFObject("{$address}/charts/amline/amline.swf", "chart7c402aad61", "680", "300", "8", "#ffffff");
      so7c402aad61.addVariable("path", "{$address}/charts/");
      so7c402aad61.addVariable("chart_settings", encodeURIComponent('<?xml version="1.0" encoding="UTF_8"?> <settings> <plot_area> <margins> <bottom>0</bottom> <top>10</top> <left>60</left> <right>60</right> </margins> </plot_area> <legend> <x>330</x> </legend> <graphs> <graph gid="1"> <line_width>2</line_width> <color>#000099</color> <title><![CDATA[CPU Usage (Cores)]]></title> <color_hover>#000099</color_hover> <selected>true</selected> </graph> </graphs> <values> <x> <color>#999999</color> </x> <y_left> <text_size>9</text_size> <color>#999999</color> <min>0</min> <max>1</max> </y_left> </values> <indicator> <x_balloon_text_color>#FFFFFF</x_balloon_text_color> <color>#999999</color> <line_alpha>50</line_alpha> <selection_color>#0000DD</selection_color> <selection_alpha>20</selection_alpha> </indicator> <font_size>10</font_size> <font>Tahoma</font> <height>300</height> <balloon> <only_one>true</only_one> </balloon> <decimals_separator>.</decimals_separator> <axes> <x> <color>#999999</color> <width>0</width> </x> <y_left> <color>#999999</color> <width>1</width> </y_left> </axes> <background_color>#ffffff</background_color> <grid> <x> <enabled>true</enabled> </x> <y_right> <enabled>false</enabled> </y_right> </grid> <width>680</width> </settings> '));
      so7c402aad61.addVariable("chart_data", encodeURIComponent("<chart><xaxis>{$xaxis}</xaxis><graphs><graph gid='1'>{$yaxis}</graph></graphs></chart>"));
      so7c402aad61.write("chart7c402aad61");
      // ]]>
      </script>
{else}
<div align="center"><strong>No data found</strong></div>
{/if}
<br/>
