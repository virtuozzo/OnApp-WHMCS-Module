<link href="modules/servers/onapp/includes/power_buttons.css" rel="stylesheet" type="text/css">
<link href="modules/servers/onapp/includes/overview.css" rel="stylesheet" type="text/css">
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>To add a new Virtual Machine, complete the form below and click the Create Virtual Machine button at the bottom of the page.</p>
<h2 class="heading2">Virtual Machine Details</h2>
<form name="create_vm" method="get" action="onapp.php">
    <input type="hidden" name="page" value="productdetails">
    <input type="hidden" name="id" value="{$service.id}">
    <input type="hidden" name="action" value="create">
<table cellspacing="5" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr class="vm-overview">
      <td>&nbsp;</td>
      <td><div class="hostname"><strong>Hostname</strong></div></td>
      <td>
{if $service.domain != "" }
      <td>
          {$service.domain}
          <input type="hidden" name="domain" value="{$service.domain}">
      </td>
{else}
      <td>
          <input type="text" size="15" name="domain">
      </td>
{/if}
      </td>
      <td>&nbsp;</td>
      <td><div class="template"><strong>Template</strong></div></td>
      <td>
{ if count($templates) > 1 }
        <select name="templateid">
{foreach from=$templates key=key item=template}
            <option value="{$key}">{$template->_label}</option>
{/foreach}
        </select>
{else}
{foreach from=$templates key=key item=template}
            <input type="hidden" name="templateid" value="{$key}">
            {$template->_label}
{/foreach}
{/if}
      </td>
    </tr>
  </tbody>
</table>
<h2 class="heading2">Virtual Machine Settings</h2>
<table cellspacing="5" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Memory</strong></td
      <td>
      {if $service.configoption3 eq "" }
        {$service.additionalram} MB
      {else}
        {math equation="x + y" x=$service.configoption3 y=$service.additionalram} MB
      {/if}
      </td>
      <td>&nbsp;</td>
      <td><strong>CPU(s)</strong></td>
      <td>
      {if $service.configoption5 eq "" }
        {$service.additionalcpus} CPU(s)
      {else}
        {math equation="x + y" x=$service.configoption5 y=$service.additionalcpus} CPU(s)
      {/if}
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Network Speed</strong></td>
      <td>{$service.configoption8} Mbps ( Unlimited if not set )</td>
      <td>&nbsp;</td>
      <td><strong>CPU Priority</strong></td>
      <td>
      {if $service.configoption7 eq ""}
        {$service.additionalcpushares} %
      {else}
        {math equation="x + y" x=$service.configoption7 y=$service.additionalcpushares} %
      {/if}
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Primary disk size</strong></td>
      <td>
      {if $service.configoption11 eq ""}
        {$service.additionaldisksize} GB
      {else}
        {math equation="x + y" x=$service.configoption11 y=$service.additionaldisksize} GB
       {/if}
      </td>
      <td>&nbsp;</td>
      <td><strong>Swap disk size</strong></td>
      <td>{$service.configoption9} GB</td>
    </tr>
  </tbody>
</table>
</form>
<h2 class="heading2">Actions</h2>
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td align="center">
        <a href="#" onClick="create_vm.submit();; return false;">Create Virtual Machine</a>
      </td>
    </tr>
  </tbody>
</table>
