<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a href="onapp.php?page=productdetails&id={$id}">Overview</a>
    | <a href="onapp.php?page=cpuusage&id={$id}">CPU Usage</a>
    | <strong>IP Addresses</strong>
    | <a href="onapp.php?page=disks&id={$id}">Disks</a>
    | <a href="onapp.php?page=backups&id={$id}">Backups</a>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>This page lists the IP Addresses allocated to this Virtual Machine. When a machine is built, these will automatically be configured. However, if they are allocated after the machine has been built, you will need to configure them yourself.</p>
<h2 class="heading2">IP Addresses for this Virtual Machine</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>ID #</th>
    <th>IP Address</th>
    <th>Netmask</th>
    <th>Gateway</th>
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
<h2 class="heading2" style="color: red;">{ $not_resolved_ips|@count } Not Resolved IP Addresses for this Virtual Machine</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>ID #</th>
    <th>IP Address</th>
    <th>Netmask</th>
    <th>Gateway</th>
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
        <a href="onapp.php?page=ipaddresses&id={$id}&action=resolve&ipid={$not_resolved_ip->_id}">Resolve</a> |
{elseif $service.configoption17 != 0 }
        <a href="cart.php?gid=addons&pid={$id}">Buy</a> |
{/if}
        <a href="onapp.php?page=ipaddresses&id={$id}&action=delete&ipid={$not_resolved_ip->_id}">Delete</a>
    </td>
  </tr>
{/foreach}
</table>
<div align="right">
    <a href="onapp.php?page=ipaddresses&id={$id}&action=resolveall">Resolve All</a>&nbsp;
</div>
<br/>
{elseif count($not_resolved_addons) > 0 }
<h2 class="heading2" style="color: red;">{ $not_resolved_addons|@count } Not Resolved IP Addresses Addons for this Virtual Machine</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
  <tr>
    <th>ID #</th>
    <th>Pricing</th>
    <th>Next Due Date</th>
    <th>&nbsp;</th>
  </tr>
{foreach item=not_resolved_addon key=ID from=$not_resolved_addons}
  <tr>
    <td>{$ID}</td>
    <td>{$not_resolved_addon.pricing}</td>
    <td>{$not_resolved_addon.nextduedate}</td>
    <td>
        <a href="onapp.php?page=ipaddresses&id={$id}&action=resolveaddon&addonid={$not_resolved_addon.id}">Add IP</a>
    </td>
  </tr>
{/foreach}
</table>
{elseif $service.configoption17 != 0 }
<div align="right">
  <a href="cart.php?gid=addons&pid={$id}">Add New IP Address</a>&nbsp;
</div>
<br/>
{/if}
