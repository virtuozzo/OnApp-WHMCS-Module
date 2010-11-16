<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="onapp.php?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="onapp.php?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="onapp.php?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <strong>Backups</strong>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="onapp.php?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
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
{if $backup->_built == "true" }
        Built
{else}
        Pending
{/if}
    </td>
    <td>
{if $backup->_built != "true"}
        not built yet
{elseif $backup->_backup_size gt 1024}
        { $backup->_backup_size/1024|round } MB
{else}
        { $backup->_backup_size} K
{/if}
    </td>
    <td>
{if $backup->_built != "true"}
        &nbsp;
{else}
        { $backup->_backup_type }
{/if}
    </td>
    <td>
{if $backup->_built != "true"}
        &nbsp;
{else}
       <a href="onapp.php?page=backups&id={$id}&action=restore&backupid={$backup->_id}">{$LANG.onapprestore}</a>
{/if}
    </td>
</tr>
{foreachelse}
  <tr>
    <td colspan="6">{$LANG.norecordsfound}</td>
  </tr>
{/foreach}
</table>
