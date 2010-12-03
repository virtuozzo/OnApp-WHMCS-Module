<link href="includes/jscript/css/ui.all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/jscript/jqueryui.js"></script>
<script type="text/javascript" src="modules/servers/onapp/includes/slider_upgrade.js"></script>
<div class="contentbox">
    <a title="{$LANG.onappoverview}" href="onapp.php?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="onapp.php?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="onapp.php?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="onapp.php?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="onapp.php?page=backups&id={$id}">{$LANG.onappbackups}</a>
    | <strong>{$LANG.onappupgradedowngrade}</strong>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappupgradedowngradetitle}</p>
<h2 class="heading2">{$LANG.onappresources}</h2>
<form action="upgrade.php" method="post">
<input type="hidden" value="2" name="step">
<input type="hidden" value="configoptions" name="type">
<input type="hidden" value="{$id}" name="id">
<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%">
<table cellspacing="0" cellpadding="10" border="0" width="100%">
  <tr>
    <th>{$LANG.supportticketsclientname}</th>
    <th>{$LANG.onappincludedinpack}</th>
    <th>{$LANG.onappadditional}</th>
  </tr>
  <tbody>
    {foreach from=$configoptions key=id item=configoption}
    {if $id neq $service.configoption19 }
    <tr>
      <td><strong>{$configoption.name}</strong></td>
      <td>{$configoption.order} {$configoption.prefix}</td>
      <td>
        {if $configoption.optiontype eq 1}
        <select style="margin:5px 15px 0 5px; width:270px;" width="270" name="configoption[{$id}]">
        {foreach from=$configoption.options item=option}
          {if $option.id eq $configoption.active}
            <option selected='selected' value="{$option.id}">{$option.name} ({$LANG.upgradenochange})</option>
          {else}
            <option value="{$option.id}">{$option.name}</option>
          {/if}
        {/foreach}
        </select>
        {elseif $configoption.optiontype eq 2}
        {foreach from=$configoption.options item=option}
          {if $option.id eq $configoption.active}
            <input style="margin:5px 5px 0 5px;" type="radio" checked="checked" value="{$option.id}" name="configoption[{$id}]">{$option.name}&nbsp;({$LANG.upgradenochange})<br />
          {else}
            <input style="margin:5px 5px 0 5px;" type="radio" value="{$option.id}" name="configoption[{$id}]">{$option.name}<br />
          {/if}
        {/foreach}
        {elseif $configoption.optiontype eq 3}
        {elseif $configoption.optiontype eq 4}
        <div class="input-with-slider">
          <input type="text" value="{$configoption.value}" size="4" name="configoption[{$id}]" readonly="">
          <div class="slider" style="float:left; margin:5px 15px 0 5px; width:200px;" max="{$configoption.max}" min="{$configoption.min}" step="{$configoption.step}" target="configoption[{$id}]" width="200">
          </div>
        </div>
        {/if}
      </td>
    </tr>
    {/if}
    {/foreach}
  </tbody>
</table>
<br/>

{assign var='optionid' value=$service.configoption19}
{if $optionid neq 0 && $configoptions.$optionid.optiontype eq 1}
<h2 class="heading2">{$LANG.onapptemplate}</h2>
<table cellspacing="0" cellpadding="10" border="0" width="100%">
  <tr>
    <th>&nbsp;</th>
    <th>{$LANG.upgradecurrentconfig}</th>
    <th>&nbsp</th>
    <th>{$LANG.upgradenewconfig}</th>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
        {assign var='os' value=$service.os}
        {$templates.$os->_label}
    </td>
    <td> =&gt; </td>
    <td align="center">
      {assign var='configoption' value=$configoptions.$optionid}

      <select style="margin:5px 15px 0 5px; width:270px;" width="270" name="configoption[{$service.configoption19}]">
      {foreach from=$configoption.options item=option}
        {if $option.id eq $configoption.active}
          <option selected='selected' value="{$option.id}">{$option.name} ({$LANG.upgradenochange})</option>
        {else}
          <option value="{$option.id}">{$option.name}</option>
        {/if}
      {/foreach}
      </select>
    </td>
  </tr>
</table>
<br/>
{/if}

<br/>
<input type="submit" value="{$LANG.ordercontinuebutton}">
</form>
<br/>