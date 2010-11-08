<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<div class="contentbox">
    <a href="onapp.php?page=productdetails&id={$id}">Overview</a>
    | <a href="onapp.php?page=cpuusage&id={$id}">CPU Usage</a>
    | <a href="onapp.php?page=ipaddresses&id={$id}">IP Addresses</a>
    | <strong>Disks</strong>
    | <a href="onapp.php?page=backups&id={$id}">Backups</a>
</div>
<p>This page lists the Disks in your OnApp cluster. Click the icons to edit Disk size, manage Backups and Schedules, and delete the Disk. Use caution when changing Disk settings. Please consult OnApp support if you're unsure about anything (support@onapp.com).</p>
<h2 class="heading2">Disks Settings</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
<tr>
    <th>Disk</th>
    <th>Size</th>
    <th>Type</th>
    <th>Built?</th>
<!--    <th>Backups</th> -->
    <th>Autobackup?</th>
    <th>&nbsp;</th>
</tr>
{foreach item=disk from=$disks}
<tr>
    <td>#{$disk->_id}</td>
    <td align="right">{$disk->_disk_size} GB</td>
    <td>
{if $disk->_primary == "true"}
        Standard (primary)
{elseif $disk->_is_swap == "true"}
        Swap
{else}
        Standard
{/if}
    </td>
    <td>
{if $disk->_built == "true"}
        YES
{else}
        NO
{/if}
    </td>
<!--    <td></td> -->
    <td>
{if $disk->_has_autobackups == "true"}
        <a class="power off-inactive" rel="nofollow">NO</a>
        <a class="power on-active">YES</a>
{else}
        <a class="power off-active">NO</a>
        <a class="power on-inactive" rel="nofollow">YES</a>
{/if}
    </td>
    <td>
{if $disk->_is_swap != "true"}
      <a title="Backups" href="{$smarty.server.PHP_SELF}?page=backups&id={$id}&diskid={$disk->_id}&action=add">
        <img style="border: none;" title="" src="modules/servers/onapp/includes/backup.png" alt=""/>
      </a>
{/if}
    </td>
</tr>
{foreachelse}
  <tr>
    <td colspan="6">{$LANG.norecordsfound}</td>
  </tr>
{/foreach}
</table>
