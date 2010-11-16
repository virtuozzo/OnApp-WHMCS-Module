<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="onapp.php?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="onapp.php?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <strong>IP Addresses</strong>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="onapp.php?page=backups&id={$id}">{$LANG.onappbackups}</a>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="onapp.php?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappipaddressestitle}</p>
<h2 class="heading2">{$LANG.onappvmipaddresses}</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>{$LANG.onappid} #</th>
    <th>{$LANG.onappipaddress}</th>
    <th>{$LANG.onappnetmask}</th>
    <th>{$LANG.onappgateway}</th>
    <th>&nbsp;</th>
  </tr>
{foreach item=resolved_ip key=ID from=$resolved_ips}
  <tr>
    <td>{$resolved_ip.ip->_id}</td>
    <td>{$resolved_ip.ip->_address}</td>
    <td>{$resolved_ip.ip->_netmask}</td>
    <td>{$resolved_ip.ip->_gateway}</td>
    <td>&nbsp;</td>
  </tr>
{foreachelse}
  <tr>
    <td colspan="6">{$LANG.norecordsfound}</td>
  </tr>
{/foreach}
</table>
<br/>

{if count($not_resolved_ips) > 0 }
<h2 class="heading2" style="color: red;">{ $not_resolved_ips|@count } {$LANG.onappnotresolvedips}</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>{$LANG.onappid} #</th>
    <th>{$LANG.onappipaddress}</th>
    <th>{$LANG.onappnetmask}</th>
    <th>{$LANG.onappgateway}</th>
    <th>&nbsp;</th>
  </tr>
{foreach item=not_resolved_ip from=$not_resolved_ips}
  <tr>
    <td>{$not_resolved_ip->_id}</td>
    <td>{$not_resolved_ip->_address}</td>
    <td>{$not_resolved_ip->_netmask}</td>
    <td>{$not_resolved_ip->_gateway}</td>
    <td>
{if count($not_resolved_addons) > 0 }
        <a href="onapp.php?page=ipaddresses&id={$id}&action=resolve&ipid={$not_resolved_ip->_id}">{$LANG.onappresolve}</a> |
{elseif $service.configoption17 != 0 }
        <a href="cart.php?gid=addons&pid={$id}">{$LANG.onappbuy}</a> |
{/if}
        <a href="onapp.php?page=ipaddresses&id={$id}&action=delete&ipid={$not_resolved_ip->_id}">{$LANG.onappdelete}</a>
    </td>
  </tr>
{/foreach}
</table>
<div align="right">
    <a href="onapp.php?page=ipaddresses&id={$id}&action=resolveall">{$LANG.onappresolveall}</a>&nbsp;
</div>
<br/>
{elseif count($not_resolved_addons) > 0 }
<h2 class="heading2" style="color: red;">{ $not_resolved_addons|@count } {$LANG.onappnotresolvedipaddons}</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>{$LANG.onappid} #</th>
    <th>{$LANG.clientareaaddonpricing}</th>
    <th>{$LANG.clientareahostingnextduedate}</th>
    <th>&nbsp;</th>
  </tr>
{foreach item=not_resolved_addon key=ID from=$not_resolved_addons}
  <tr>
    <td>{$ID}</td>
    <td>{$not_resolved_addon.pricing}</td>
    <td>{$not_resolved_addon.nextduedate}</td>
    <td>
        <a href="onapp.php?page=ipaddresses&id={$id}&action=resolveaddon&addonid={$not_resolved_addon.id}">{$LANG.onappaddip}</a>
    </td>
  </tr>
{/foreach}
</table>
{elseif $service.configoption17 != 0 }
<div align="right">
  <a href="cart.php?gid=addons&pid={$id}">{$LANG.onappaddnewip}</a>&nbsp;
</div>
<br/>
{/if}
