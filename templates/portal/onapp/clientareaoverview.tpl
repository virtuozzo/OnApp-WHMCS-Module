<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<link href="modules/servers/onapp/includes/overview.css" rel="stylesheet" type="text/css">
{literal}
<script>
function showconsole(id) {
    window.open("modules/servers/onapp/console.php?id="+id,"popup","width=820,height=640,scrollbars=0,resizable=0,toolbar=0,directories=0,location=0,menubar=0,status=0,left=50,top=50");
}
</script>
{/literal}
<div class="contentbox">
    <strong>Overview</strong>
    | <a href="onapp.php?page=cpuusage&id={$id}">CPU Usage</a>
    | <a href="onapp.php?page=ipaddresses&id={$id}">IP Addresses</a>
    | <a href="onapp.php?page=disks&id={$id}">Disks</a>
    | <a href="onapp.php?page=backups&id={$id}">Backups</a>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>This page shows details of the selected Virtual Machine. The On/Off buttons change its Power status. The Actions section lets you perform common tasks. The Activity Log shows VM transactions and lets you cancel pending tasks. Use the navigation at the top of the page to explore different aspects of this Virtual Machine.</p>
<h2 class="heading2">Virtual Machine Details</h2>
<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr class="vm-overview">
      <td rowspan="2">
{if $virtualmachine->_operating_system == "linux" }
          <img src="modules/servers/onapp/includes/linux-48x48.png"   alt="Linux" height="48" width="48" />
{elseif $virtualmachine->_operating_system == "windows" }
          <img src="modules/servers/onapp/includes/windows-48x48.png" alt="Windows" height="48" width="48" />
{/if}
      </td>
      <td><div class="hostname"><strong>Hostname</strong></div></td>
      <td>
          {$virtualmachine->_hostname}
      </td>
      <td>&nbsp;</td>
      <td><div class="status"><strong>Status</strong></div></td>
      <td>
    {if $virtualmachine->_locked eq "true" || $virtualmachine->_built eq "false" }
        <a class="power pending">Pending</a>
    {elseif $virtualmachine->_booted eq "true"}
        <a rel="nofollow" class="power off-inactive" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=stop">OFF</a>
        <a class="power on-active">ON</a>
    {elseif $virtualmachine->_booted eq "false"}
        <a class="power off-active">OFF</a>
        <a rel="nofollow" class="power on-inactive" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=start">ON</a>
    {else}
      &nbsp;
    {/if}
      </td>
    </tr>
    <tr class="vm-overview">
      <td><div class="login"><strong>Login</strong></div></td>
      <td>
        <code>{if $virtualmachine->_operating_system eq "windows"}Administrator{else}root{/if}</code>
        /
        <a onclick="$('#root_password').show(); $(this).hide();; return false;" href="#" id="root_password_href">password</a>
        <a onclick="$('#root_password_href').show(); $(this).hide();; return false;" href="#"  style="display: none;" id="root_password">{$virtualmachine->_initial_root_password}</a>
      </td>
      <td>&nbsp;</td>
      <td><div class="template"><strong>Template</strong></div></td>
      <td>
{if $virtualmachine->_built eq "true" }
        {$virtualmachine->_template_label}
{else}
        Not built yet...
{/if}
      </td>
    </tr>
    <tr><td colspan="6"></td></tr>
  </tbody>
</table>
<h2 class="heading2">Virtual Machine Settings</h2>
<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Memory</strong></td>
      <td>{$virtualmachine->_memory} MB</td>
      <td>&nbsp;</td>
      <td><strong>CPU(s)</strong></td>
      <td>{$virtualmachine->_cpus} CPU(s)</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>CPU Priority</strong></td>
      <td>{$virtualmachine->_cpu_shares} %</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>

<h2 class="heading2">Actions</h2>

    {if $virtualmachine->_locked eq "true"}
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td align="center">
{literal}
        <script type="text/JavaScript">
        <!--
          window.onload = function(){
            setTimeout("location.reload(true);", 10000); 
          };
        //   -->
        </script>
{/literal}
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=unlock">Unlock Virtual Machine</a>
      </td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_built eq "false" }
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=build">Build Virtual Machine</a>
      </td>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=delete">Delete Virtual Machine</a>
      </td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_booted eq "true"}
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=stop">Shut down Virtual Machine</a>
      </td>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=reboot">Reboot Virtual Machine</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=rebuild">Rebuild Virtual Machine</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="#" onclick="showconsole('{$virtualmachine->_id}');; return false;">Virtual Machine Console</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=disks&id={$id}">Manage Disks</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=ipaddresses&id={$id}">Manage IP Addresses</a>
      </td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_booted eq "false"}
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=start">Startup Virtual Machine</a>
      </td>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=rebuild">Rebuild Virtual Machine</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=delete">Delete Virtual Machine</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=disks&id={$id}">Manage Disks</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=ipaddresses&id={$id}">Manage IP Addresses</a>
      </td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
    {/if}
<br/>
