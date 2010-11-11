<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="onapp.php?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="onapp.php?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="onapp.php?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <strong>Backups</strong>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>This page lists the Backups which have been taken or are waiting to be taken for this Virtual Machine. Click the relevant links to convert a Backup to a template, or restore the disk from a Backup. Click the icon on the right to delete that Backup.</p>
<h2 class="heading2">Backups for this Virtual Machine</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
<tr>
    <th>Date/Time</th>
    <th>Disk</th>
    <th>Status</th>
    <th>Backup Size</th>
    <th>Backup Type</th>
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
       <a href="onapp.php?page=backups&id={$id}&action=restore&backupid={$backup->_id}">Restore</a>
{/if}
    </td>
</tr>
{foreachelse}
  <tr>
    <td colspan="6">{$LANG.norecordsfound}</td>
  </tr>
{/foreach}
</table>
