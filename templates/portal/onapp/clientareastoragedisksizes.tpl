<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<div class="contentbox">
    <a title="{$LANG.onappmyvms}" href="onapp.php">{$LANG.onappmyvms}</a>
    | <strong>{$LANG.onappstoragedisksize}</strong>
</div>
<p>{$LANG.onappstoragedisksizetitle}</p>
<br>
<table class="data" width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr>
    <th width="18">&nbsp;</th>
    <th>{$LANG.onappproductandservice}</th>
    <th width="200">{$LANG.onappspacelimit}</th>
    <th width="58">&nbsp;</th>
  </tr>
  <tr>
{foreach item=server from=$rows}
    <td>&nbsp;</td>
    <td><strong>{$server.name}</strong></td>
    <td>{$server.storage_disk_size} GB / {$server.backups_size} GB ({math equation="y - x" x=$server.backups_size y=$server.storage_disk_size} GB free)</td>
    <td>&nbsp;</td>
  </tr>
  {foreach key=num item=service from=$server.services}
  <tr class="clientareatable{$service.domainstatus}">
    <td>&nbsp;</td>
    <td>{$service.product}</td>
    <td>{$service.basespace} GB + {$service.additionalspace} GB</td>
    <td align="right" width="58">
    <form action="/clientarea.php?action=productdetails" method="post">
        <input type="hidden" value="{$service.id}" name="id">
        <input type="image" src="images/viewdetails.gif">
    </form>
    </td>
  </tr>
  {foreachelse}
  <tr>
    <td colspan="4">{$LANG.norecordsfound}</td>
  </tr>
  {/foreach}
  {foreachelse}
  <tr>
    <td colspan="4">{$LANG.norecordsfound}</td>
  </tr>
  {/foreach}
</table>
<table border="0" align="center" cellpadding="10" cellspacing="0">
  <tr>
    <td width="10" align="right" class="clientareatableactive">&nbsp;</td>
    <td>{$LANG.clientareaactive}</td>
    <td width="10" align="right" class="clientareatablepending">&nbsp;</td>
    <td>{$LANG.clientareapending}</td>
    <td width="10" align="right" class="clientareatablesuspended">&nbsp;</td>
    <td>{$LANG.clientareasuspended}</td>
    <td width="10" align="right" class="clientareatableterminated">&nbsp;</td>
    <td>{$LANG.clientareaterminated}</td>
  </tr>
</table><br/>
