<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<link href="modules/servers/onapp/includes/overview.css" rel="stylesheet" type="text/css">
{literal}
<script>
function showconsole(id) {
    window.open("modules/servers/onapp/console.php?id="+id,"popup","width=820,height=640,scrollbars=0,resizable=0,toolbar=0,directories=0,location=0,menubar=0,status=0,left=50,top=50");
}

function rebuildvm(id) {
      if( confirm("{/literal}{$LANG.onappconfirmrebuildvm}{literal}") ) {
        window.location="onapp.php?page=productdetails&id={/literal}{$id}{literal}&action=rebuild";
    };
}

function deletevm(id) {
    if ( confirm("{/literal}{$LANG.onappconfirmdeletevm}{literal}") ) {
        window.location="onapp.php?page=productdetails&id={/literal}{$id}{literal}&action=delete";
    };
}

function stopvm(id) {
    if ( confirm("{/literal}{$LANG.onappconfirmstopvm}{literal}") ) {
        window.location="onapp.php?page=productdetails&id={/literal}{$id}{literal}&action=stop";
    }
}
</script>
{/literal}
<div class="contentbox">
    <strong>{$LANG.onappoverview}</strong>
    | <a title="{$LANG.onappcpuusage}" href="onapp.php?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="onapp.php?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="onapp.php?page=backups&id={$id}">{$LANG.onappbackups}</a>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="onapp.php?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappoverviewtitle}</p>
<h2 class="heading2">{$LANG.onappvmdetails}</h2>
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
      <td><div class="hostname"><strong>{$LANG.onapphostname}</strong></div></td>
      <td>
          {$virtualmachine->_hostname}
      </td>
      <td>&nbsp;</td>
      <td><div class="status"><strong>{$LANG.onappstatus}</strong></div></td>
      <td>
    {if $virtualmachine->_locked eq "true" || $virtualmachine->_built eq "false" }
        <a class="power pending">Pending</a>
    {elseif $virtualmachine->_booted eq "true"}
        <a rel="nofollow" class="power off-inactive" href="#" onclick="stopvm();; return false;">OFF</a>
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
      <td><div class="login"><strong>{$LANG.onapplogin}</strong></div></td>
      <td>
        <code>{if $virtualmachine->_operating_system eq "windows"}Administrator{else}root{/if}</code>
        /
        <a onclick="$('#root_password').show(); $(this).hide();; return false;" href="#" id="root_password_href">{$LANG.onapppassword}</a>
        <a onclick="$('#root_password_href').show(); $(this).hide();; return false;" href="#"  style="display: none;" id="root_password">{$virtualmachine->_initial_root_password}</a>
      </td>
      <td>&nbsp;</td>
      <td><div class="template"><strong>{$LANG.onapptemplate}</strong></div></td>
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
<h2 class="heading2">{$LANG.onappvmsettings}</h2>
<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappmem}</strong></td>
      <td>{$virtualmachine->_memory} MB</td>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappcpus}</strong></td>
      <td>{$virtualmachine->_cpus} CPU(s)</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappcpupriority}</strong></td>
      <td>{$virtualmachine->_cpu_shares} %</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>

<h2 class="heading2">{$LANG.onappactions}</h2>

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
<!--
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=unlock">Unlock Virtual Machine</a>
-->
        <p align="center"><b>{$LANG.onappvmlocked}</b></p>
      </td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_built eq "false" }
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=build">{$LANG.onappvmbuild}</a>
      </td>
      <td width="33%">
        <a  href="#" onclick="deletevm();; return false;">{$LANG.onappvmdel}</a>
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
        <a  href="#" onclick="stopvm();; return false;">{$LANG.onappvmstop}</a>
      </td>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=reboot">{$LANG.onappvmreboot}</a>
      </td>
      <td>
        <a href="#" onclick="rebuildvm();; return false;">{$LANG.onappvmrebuild}</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="#" onclick="showconsole('{$virtualmachine->_id}');; return false;">{$LANG.onappvmconsole}</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=disks&id={$id}">{$LANG.onappvmmanagedisks}</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=ipaddresses&id={$id}">{$LANG.onappvmmanageips}</a>
      </td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_booted eq "false"}
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td width="33%">
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=start">{$LANG.onappvmstart}</a>
      </td>
      <td width="33%">
        <a href="#" onclick="rebuildvm();; return false;">{$LANG.onappvmrebuild}</a>
      </td>
      <td>
        <a  href="#" onclick="deletevm();; return false;">{$LANG.onappvmdel}</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=disks&id={$id}">{$LANG.onappvmmanagedisks}</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=ipaddresses&id={$id}">{$LANG.onappvmmanageips}</a>
      </td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
    {/if}
<br/>
