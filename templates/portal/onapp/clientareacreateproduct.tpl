<link href="modules/servers/onapp/includes/onapp.css" rel="stylesheet" type="text/css">
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappcreatevmtitle}</p>
<h2 class="heading2">{$LANG.onappvmdetails}</h2>
<form name="create_vm" id="create_vm" method="get" action="onapp.php">
    <input type="hidden" name="page" value="productdetails">
    <input type="hidden" name="id" value="{$service.id}">
    <input type="hidden" name="action" value="create">
<table cellspacing="5" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr class="vm-overview">
      <td>&nbsp;</td>
      <td><div class="hostname"><strong>{$LANG.onapphostname}</strong></div></td>
      <td>
{if $service.domain != "" }
      <td>
          {$service.domain}
          <input type="hidden" name="hostname" value="{$service.domain}">
      </td>
{else}
      <td>
          <input type="text" size="15" name="hostname">
      </td>
{/if}
      </td>
      <td>&nbsp;</td>
      <td><div class="template"><strong>{$LANG.onapptemplate}</strong></div></td>
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
<h2 class="heading2">{$LANG.onappvmsettings}</h2>
<table cellspacing="5" cellpadding="10" border="0" align="center" width="100%">
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappmem}</strong></td>
      <td>
      {if $service.configoption3 eq "" }
        {$service.additionalram} MB
      {else}
        {math equation="x + y" x=$service.configoption3 y=$service.additionalram} MB
      {/if}
      </td>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappcpus}</strong></td>
      <td>
      {if $service.configoption5 eq "" }
        {$service.additionalcpus} {$LANG.onappcpus}
      {else}
        {math equation="x + y" x=$service.configoption5 y=$service.additionalcpus} CPU(s)
      {/if}
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappnetspeed}</strong></td>
      <td>
        {if $service.configoption8 eq 0}
          Unlimited
        {else}
          {$service.configoption8} Mbps
        {/if}
      </td>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappcpupriority}</strong></td>
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
      <td><strong>{$LANG.onappprivarydisksize}</strong></td>
      <td>
      {if $service.configoption11 eq ""}
        {$service.additionaldisksize} GB
      {else}
        {math equation="x + y" x=$service.configoption11 y=$service.additionaldisksize} GB
       {/if}
      </td>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappswapsize}</strong></td>
      <td>{$service.configoption9} GB</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>{$LANG.onappincludedips}</strong></td>
      <td>
      {if $service.configoption18 ne "" && $service.additionalips ne ""}
        {math equation="x + y" x=$service.configoption18 y=$service.additionalips} IP(s)
      {else}
         0 IP(s)
      {/if}
      </td>
      <td colspan="2">&nbsp;</td>
    </tr>
  </tbody>
</table>
</form>
<h2 class="heading2">{$LANG.onappactions}</h2>
<table cellspacing="10" cellpadding="10" border="0" width="100%">
  <tbody>
    <tr>
      <td align="center">
        <a href="#" onClick="document.getElementById('create_vm').submit(); return false;">{$LANG.onappcreatevm}</a>
      </td>
    </tr>
  </tbody>
</table>
