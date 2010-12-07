<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappproductstitle}</p>
<h2 class="heading2">{$LANG.clientareavirtualmachines}</h2>
<table class="data" width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr>
    <th width="18">&nbsp;</th>
    <th>{$LANG.onappproductandservice}</th>
    <th>{$LANG.onappipaddresses}</th>
    <th width="70">{$LANG.onapppower}</th>
    <th>{$LANG.onappram}</th>
    <th width="58">&nbsp;</th>
  </tr>
{foreach key=num item=service from=$services}
  <tr>
    <td>
    {if not isset($service.obj)}
      &nbsp;
    {elseif $service.obj->_booted eq "true" }
      <img title="" src="modules/servers/onapp/includes/on.png" alt="">
    {elseif $service.obj->_booted eq "false"}
      <img title="" src="modules/servers/onapp/includes/off.png" alt="">
    {else}
      &nbsp;
    {/if}
    </td>
    <td>{$service.product}<br/>
    {if isset($service.obj)}
      <a href="http://{$service.obj->_label}" target="_blank">{$service.obj->_label}</a>
    {else}
      <a href="http://{$service.domain}" target="_blank">{$service.domain}</a> 
    {/if}
    </td>
{if isset($service.obj)}
    <td>
    {foreach item=ip_addresses from=$service.obj->_ip_addresses}
      {$ip_addresses->_address}<br/>
    {foreachelse}
      -
    {/foreach}
    </td>
    <td>
    {if $service.obj->_locked eq "true" || $service.obj->_built eq "false"}
        <a class="power pending">Pending</a>
    {elseif $service.obj->_booted eq "true"}
        <a rel="nofollow" class="power off-inactive" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$num}&action=stop">OFF</a>
        <a class="power on-active">ON</a>
    {elseif $service.obj->_booted eq "false"}
        <a class="power off-active">OFF</a>
        <a rel="nofollow" class="power on-inactive" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$num}&action=start">ON</a>
    {else}
      &nbsp;
    {/if}
    </td>
    <td>
      {$service.obj->_memory} MB
    </td>
{elseif isset($service.error)}
    <td colspan="3" color="red">
    {$service.error}
    </td>
{else}
    <td colspan="3">&nbsp;</td>
{/if}
    <td align="right">
      <a title="View" href="{$smarty.server.PHP_SELF}?page=productdetails&id={$num}">
        <img style="border: none;" title="" src="images/viewdetails.gif" alt=""/>
      </a>
    </td>
  </tr>
  {foreachelse}
  <tr>
    <td colspan="7">{$LANG.norecordsfound}</td>
  </tr>
  {/foreach}
</table>
<br/>

{if count($not_resolved_vms) > 0 }
<h2 class="heading2" style="color: red;">{$LANG.onappnotresolvedvms}</h2>
<table class="data" width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr>
    <th>{$LANG.onappid} #</th>
    <th>{$LANG.onappservername}</th>
    <th>{$LANG.onapphostname}</th>
    <th>{$LANG.onapppower}</th>
  </tr>
{foreach item=vms from=$not_resolved_vms} {foreach item=vm from=$vms}
  <tr>
    <td>{$vm.vm->_id}</td>
    <td>{$vm.server.name}</td>
    <td>{$vm.vm->_hostname}</td>
    <td align="center">
      {if $vm.vm->_locked eq "true" || $vm.vm->_built eq "false"}
        <a class="power pending">Pending</a>
      {elseif $vm.vm->_booted eq "true"}
        <a rel="nofollow" class="power off-inactive">OFF</a>
        <a class="power on-active">ON</a>
      {elseif $vm.vm->_booted eq "false"}
        <a class="power off-active">OFF</a>
        <a rel="nofollow" class="power on-inactive">ON</a>
      {else}
        &nbsp;
      {/if}
    </td>
  </tr>
{/foreach}{/foreach}
</table>
{/if}

