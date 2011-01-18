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
    <th width="100">{$LANG.onappspacelimit}</th>
    <th width="58">&nbsp;</th>
  </tr>
  {foreach key=num item=service from=$rows}
  <tr class="clientareatable{$service.domainstatus}">
    <td>&nbsp;</td>
    <td>{$service.product}</td>
    <td>&nbsp;</td>
    <td align="right" width="58">
      <a title="View" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$service.id}">
        <img style="border: none;" title="" src="images/viewdetails.gif" alt=""/>
      </a>
    </td>
  </tr>
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
</table><br />
