<link href="modules/servers/onapp/includes/onapp.css" rel="stylesheet" type="text/css">

<link href="includes/jscript/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/jscript/jqueryui.js"></script>

{literal}
<script>
$(document).ready(function(){
    $("#dialog-confirm").dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    width: 430,
	buttons: {
        '{/literal}{$LANG.onappvmrebuildnetwork}{literal}': function() {
            $('#rebuildnetwork').submit();
		},
        }
    });
});

function showconsole(id) {
    window.open("modules/servers/onapp/console.php?id="+id,"popup","width=820,height=640,scrollbars=0,resizable=0,toolbar=0,directories=0,location=0,menubar=0,status=0,left=50,top=50");
}

function rebuildvm(id) {
      if( confirm("{/literal}{$LANG.onappconfirmrebuildvm}{literal}") ) {
        window.location="{/literal}{$smarty.const.ONAPP_FILE_NAME}{literal}?page=productdetails&id={/literal}{$id}{literal}&action=rebuild";
    };
}

function deletevm(id) {
    if ( confirm("{/literal}{$LANG.onappconfirmdeletevm}{literal}") ) {
        window.location="{/literal}{$smarty.const.ONAPP_FILE_NAME}{literal}?page=productdetails&id={/literal}{$id}{literal}&action=delete";
    };
}

function stopvm(id) {
    if ( confirm("{/literal}{$LANG.onappconfirmstopvm}{literal}") ) {
        window.location="{/literal}{$smarty.const.ONAPP_FILE_NAME}{literal}?page=productdetails&id={/literal}{$id}{literal}&action=stop";
    }
}

function show_logs( id, logid, date, action, status, type ) { 
                    
    jQuery.ajax( {
        url: document.location.href,
        data: 'transactionid=' + id + '&type=' + type,
        success: function( data ) {
            
        data = JSON.parse( data )
        jQuery('.log_details').remove()
        var html = '<h4>{/literal}{$LANG.onapploginfo}{literal}</h4>'+
            '<div class="log_info"><pre>' + '\n' +
                'Log ID:\t' + logid       + '\n' +
                'Type:\t'   + type        + '\n' +    
                'Date:\t'   + date        + '\n' +
                'Action:\t' + action      + '\n' +
                'Status:\t' + status      + '\n' +                                     
            '</pre></div>'                + '\n' +
                '<h4>{/literal}{$LANG.onappoutput}{literal}</h4>'+        
            '<div class="log_details"><pre>' + '\n' +
                 data.output                 + '\n' +
            '</div>'
                                
            jQuery('#vm_logs').html(html)
        }
     });
}

function showDialog(name) {
    $("#"+name).dialog('open');
}
</script>
{/literal}
<div class="contentbox">
    <strong>{$LANG.onappoverview}</strong>
    | <a title="{$LANG.onappcpuusage}" href="{$smarty.const.ONAPP_FILE_NAME}?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="{$smarty.const.ONAPP_FILE_NAME}?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="{$smarty.const.ONAPP_FILE_NAME}?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="{$smarty.const.ONAPP_FILE_NAME}?page=backups&id={$id}">{$LANG.onappbackups}</a>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="{$smarty.const.ONAPP_FILE_NAME}?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
    | <a title="{$LANG.onappfirewallrules}" href="{$smarty.const.ONAPP_FILE_NAME}?page=firewallrules&id={$id}">{$LANG.onappfirewall}</a>
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
    {if $virtualmachine->_locked eq true || $virtualmachine->_built eq false }
        <a class="power pending">Pending</a>
    {elseif $virtualmachine->_booted eq true}
        <a rel="nofollow" class="power off-inactive" href="#" onclick="stopvm();; return false;">OFF</a>
        <a class="power on-active">ON</a>
    {elseif $virtualmachine->_booted eq false}
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
{if $virtualmachine->_built eq true }
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
<form id="product_details_form" method="post" action="clientarea.php?action=productdetails">
        <input type="hidden" name="id" value={$id}]">
</form>
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

      <td><strong>{$LANG.onappportspeed}</strong></td>
      <td>
        {if $rate_limit neq 0}
           {$rate_limit} Mbps
        {else}
           {$LANG.onappunlimited}
        {/if}
      </td>
    </tr>
  </tbody>
</table>

<h2 class="heading2">{$LANG.onappactions}</h2>

    {if $virtualmachine->_locked eq true}
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
{*
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=unlock">Unlock Virtual Machine</a>
*}
        <p align="center"><b>{$LANG.onappvmlocked}</b></p>
      </td>
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_built eq false }
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
    
    {elseif $virtualmachine->_booted eq true}
    
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
        <a href="#" onclick="showconsole('{$virtualmachine->_id}'); return false;">{$LANG.onappvmconsole}</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=disks&id={$id}">{$LANG.onappvmmanagedisks}</a>
      </td>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=ipaddresses&id={$id}">{$LANG.onappvmmanageips}</a>
      </td>
    </tr>
    <tr>
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=reset_pass">{$LANG.onappvmresetpassword}</a>
      </td>
      <td>
        <a href="#" onClick="showDialog('dialog-confirm');return false">{$LANG.onappvmrebuildnetwork}</a>
      </td>
      {if $overagesenabled != 0}
      <td>
        <a href="#" onclick="$('form#product_details_form').submit(); return false;"> {$LANG.onappbwusage}</a>
      </td>
      {/if}     
    </tr>
  </tbody>
</table>
    {elseif $virtualmachine->_booted eq false}
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
      <td>
        <a href="{$smarty.server.PHP_SELF}?page=productdetails&id={$id}&action=reset_pass">{$LANG.onappvmresetpassword}</a>
      </td>
    </tr>
    <tr> 
      {if $overagesenabled != 0}
      <td>
        <a href="#" onclick="$('form#product_details_form').submit(); return false;"> {$LANG.onappbwusage} </a>
      </td>
      {/if} 
    </tr>
  </tbody>
</table>
    {/if}
    <h2 class="heading2">{$LANG.onappvmactivitylog}</h2>
    <h5>{$LANG.onappvmactivityloginfo}</h5> 
<table class="data" cellspacing="0" cellpadding="10" border="0" width="100%">
    <tr>
        <th>{$LANG.onappref}</th>
        <th>{$LANG.onappdate}</th>
        <th>{$LANG.onappaction}</th>
        <th>{$LANG.onappstatus}</th>
    </tr>    
{if $vm_logs eq false}
    <tr><td>No logs found.</td></tr>
{else}
{foreach from=$vm_logs key=logid item=log}
    <tr>
        <td>
            <a class="logdetailslink" onclick="show_logs({$log.target_id}, {$logid}, '{$log.created_at}', '{$log.action}', '{$log.status}', '{$log.target_type}'); return false;" href="#output"  >
                {$logid}
            </a>
        </td>
        <td>{$log.created_at}</td>
        <td>{$log.action}</td>
        <td>{$log.status}</td>
    </tr>
{/foreach}
          
{/if}
       
</table> 

<div id="vm_logs"></div>
<a name="output"> </a>
<br/>

<div id="dialog-confirm" title="{$LANG.onappvmrebuildnetwork}">
  <p>
      <form id="rebuildnetwork" action="{$smarty.const.ONAPP_FILE_NAME}">
          <input type="hidden" name="id" value="{$id}">
          <input type="hidden" name="page" value="productdetails">
          <input type="hidden" name="action" value="rebuild_network">

          <span style="padding-right: 2px;">{$LANG.onappvmstop}</span>
          <select name="shutdown_type">
              <option value="">{$LANG.onappdonotshutdownvm}</option>
              <option value="hard">{$LANG.onapppoweroffvm}</option>
              <option value="soft">{$LANG.onappshutdownvm}</option>
              <option value="graceful">{$LANG.onappgracefullyshutdownvm}</option>
          </select>
          <br/>
          <br/>
          <span style="padding-right: 22px;">{$LANG.onappvmstart}</span>
          <select name="required_startup">
              <option value="0">{$LANG.no}</option>
              <option value="1">{$LANG.yes}</option>
          </select>
      </p>
</div>
