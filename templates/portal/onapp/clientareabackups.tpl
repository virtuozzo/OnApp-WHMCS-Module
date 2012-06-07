<link href="modules/servers/onapp/includes/onapp.css" rel="stylesheet" type="text/css">
{literal}
<script>
function restoreback(id) {
      if( confirm("{/literal}{$LANG.onappconfirmrestoreback}{literal}") ) {
        window.location="{/literal}{$smarty.const.ONAPP_FILE_NAME}{literal}?page=backups&id={/literal}{$id}{literal}&action=restore&backupid="+id;
    };
}

function deleteback(id) {
    if ( confirm("{/literal}{$LANG.onappconfirmdeleteback}{literal}") ) {
        window.location="{/literal}{$smarty.const.ONAPP_FILE_NAME}{literal}?page=backups&id={/literal}{$id}{literal}&action=delete&backupid="+id;
    };
}
</script>
{/literal}
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="{$smarty.const.ONAPP_FILE_NAME}?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="{$smarty.const.ONAPP_FILE_NAME}?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="{$smarty.const.ONAPP_FILE_NAME}?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="{$smarty.const.ONAPP_FILE_NAME}?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <strong>{$LANG.onappbackups}</strong>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="{$smarty.const.ONAPP_FILE_NAME}?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
    | <a title="{$LANG.onappfirewallrules}" href="{$smarty.const.ONAPP_FILE_NAME}?page=firewallrules&id={$id}">{$LANG.onappfirewall}</a>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappbackupstitle}</p>
<h2 class="heading2">{$LANG.onappvmbackups}</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
<tr>
    <th>{$LANG.onappdatetime}</th>
    <th>{$LANG.onappdisk}</th>
    <th>{$LANG.onappstatus}</th>
    <th>{$LANG.onappbackupsize}</th>
    <th>{$LANG.onappbackuptype}</th>
    <th>&nbsp;</th>
</tr>
{foreach item=backup from=$backups}
<tr>
    <td>{$backup->_created_at|regex_replace:"/[TZ]/":' '}</td>
    <td>#{$backup->_disk_id}</td>
    <td>
{if $backup->_built == true }
        Built
{else}
        Pending
{/if}
    </td>
    <td>
{if $backup->_built != true}
        not built yet
{elseif $backup->_backup_size gt 1024}
        { $backup->_backup_size/1024|round } MB
{else}
        { $backup->_backup_size} K
{/if}
    </td>
    <td>
{if $backup->_built != true}
        &nbsp;
{else}
        { $backup->_backup_type }
{/if}
    </td>
    <td>
{if $backup->_built neq true}
        &nbsp;
{else}
       <a href="#" onclick="restoreback({$backup->_id});; return false;">{$LANG.onapprestore}</a> |
       <a href="#" onclick="deleteback({$backup->_id});; return false;">{$LANG.onappdelete}</a>
{/if}
    </td>
</tr>
{foreachelse}
  <tr>
    <td colspan="6">{$LANG.norecordsfound}</td>
  </tr>
{/foreach}
</table>
