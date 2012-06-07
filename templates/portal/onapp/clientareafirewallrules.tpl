<link href="modules/servers/onapp/includes/onapp.css" rel="stylesheet" type="text/css">
{literal}
<script>

</script>
{/literal}

<div class="contentbox">{}
    <a title="{$LANG.onappoverview}" href="{$smarty.const.ONAPP_FILE_NAME}?page=productdetails&id={$id}">{$LANG.onappoverview}</a>
    | <a title="{$LANG.onappcpuusage}" href="{$smarty.const.ONAPP_FILE_NAME}?page=cpuusage&id={$id}">{$LANG.onappcpuusage}</a>
    | <a title="{$LANG.onappipaddresses}" href="{$smarty.const.ONAPP_FILE_NAME}?page=ipaddresses&id={$id}">{$LANG.onappipaddresses}</a>
    | <a title="{$LANG.onappdisks}" href="{$smarty.const.ONAPP_FILE_NAME}?page=disks&id={$id}">{$LANG.onappdisks}</a>
    | <a title="{$LANG.onappbackups}" href="{$smarty.const.ONAPP_FILE_NAME}?page=backups&id={$id}">{$LANG.onappbackups}</a>
    {if $configoptionsupgrade eq 'on'}  | <a title="{$LANG.onappupgradedowngrade}" href="{$smarty.const.ONAPP_FILE_NAME}?page=upgrade&id={$id}">{$LANG.onappupgradedowngrade}</a> {/if}
    | <b>{$LANG.onappfirewall}</b>
</div>
{if isset($error)}
<div class="errorbox">
    {$error}
</div>
{/if}
<p>{$LANG.onappfirewallrulestitle}</p>
<h2 class="heading2">{$LANG.onappfirewallrules}</h2>

<form method="post" action="{$smarty.const.ONAPP_FILE_NAME}?page=firewallrules&id={$id}&action=save" >
    <div class="f_left"> <b>{$LANG.onappinterface}</b><br />
        <select name="fr[network_interface_id]" >
            {foreach from=$networkinterfaces item=interface}
                <option value="{$interface->_id}" > {$interface->_label}</option>
            {/foreach}
        </select>
    </div>    

    <div class="f_left">
        <b>{$LANG.onappcommand}</b><br />
        <select name="fr[command]" >
            <option value="ACCEPT" >ACCEPT</option>
            <option value="DROP">DROP</option>
        </select>        
    </div>   
        
    <div class="f_left">
        <b>{$LANG.onappsourceaddress}</b><br />
        <input id="fr_address" type="text" name="fr[address]" />         
    </div>
        
    <div class="f_left">
        <b>{$LANG.onappdestinationport}</b><br />
        <input id="fr_port" type="text" name="fr[port]" />         
    </div>
        
   <div class="f_left">
        <b>{$LANG.onappprotocol}</b><br />
       
        <select id="fr_protocol" name="fr[protocol]">
            <option value="TCP">TCP</option>
            <option value="UDP">UDP</option>
        </select>        
    </div>  
        
    <div class="f_left"><br />
       <input type="submit" value="{$LANG.onappsave}" />
    </div> 
    
    </form>

<div style="clear:both"></div> 
<br /><br /><br />    
    

<table cellspacing="0" cellpadding="10" border="0" width="100%" class="data">
           <tr>
                <th>{$LANG.onapprule} #</th>
                <th>{$LANG.onappsourceaddress}</th>
                <th>{$LANG.onappdestinationport}</th>
                <th>{$LANG.onappprotocol}</th>
                <th>{$LANG.onappcommand}</th>
               <th></th>
               <th></th>
           </tr>
       {foreach from=$firewall_by_network item=firewall_obj}
           {assign var=firewall_obj_count  value=$firewall_obj|@count} 
              
           {foreach from=$firewall_obj item=firewall}
               {assign var=interface_id value=$firewall->_network_interface_id}
               <tr>
                   <td>
                      {$networkinterfaces.$interface_id->_label} #{$firewall->_position}
                   </td>
                   <td>
                       {$firewall->_address}
                   </td>
                   <td>
                       {$firewall->_port}
                   </td>
                   <td>
                       {$firewall->_protocol}
                   </td>
                   <td>
                       {$firewall->_command}
                   </td>
                   
                   <td class="controll_td">
                       {if $firewall_obj_count == 1}
                           <a>
                               <img src="modules/servers/onapp/includes/up-arrow-disabled.png" />
                           </a>
                           <a>
                               <img src="modules/servers/onapp/includes/down-arrow-disabled.png" />
                           </a>
                       {elseif $firewall_obj_count == 2}
                           {if $firewall->_position  == 1}
                               <a>
                                   <img src="modules/servers/onapp/includes/up-arrow-disabled.png" />
                               </a>
                               <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=down">
                                   <img title="asdfasdfasdfasdf" src="modules/servers/onapp/includes/down-arrow.png" />
                               </a>
                           {else}
                               <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=up">
                                   <img title="adfasdfasdf" src="modules/servers/onapp/includes/up-arrow.png" />
                               </a>
                               <a>
                                   <img src="modules/servers/onapp/includes/down-arrow-disabled.png" />
                               </a>
                               
                           {/if}
                       {else}
                           {if $firewall->_position  == 1}
                                <a>
                                   <img src="modules/servers/onapp/includes/up-arrow-disabled.png" />
                               </a>
                               <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=down">
                                   <img title="adsfasdfasf" src="modules/servers/onapp/includes/down-arrow.png" />
                               </a>
                           
                           {elseif $firewall->_position == $firewall_obj_count}
                                <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=up">
                                   <img title="asdfasdfadsfad" src="modules/servers/onapp/includes/up-arrow.png" />
                               </a>
                               <a>
                                   <img src="modules/servers/onapp/includes/down-arrow-disabled.png" />
                               </a>
                           {else}
                               <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=up">
                                   <img title="asdfasdfasf" src="modules/servers/onapp/includes/up-arrow.png" />
                               </a>
                               <a href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=move&position=down">
                                   <img title="adfasdfadsf" src="modules/servers/onapp/includes/down-arrow.png" />
                               </a>
                           {/if}
                       {/if}
                       
                   </td>
                   <td class="controll_td">
                        <a title="Delete" href="{$smarty.server.PHP_SELF}?page=firewallrules&id={$id}&ruleid={$firewall->_id}&action=delete">
                            <img title="" src="modules/servers/onapp/includes/delete.png" alt=""/>
                        </a>    
                   </td>
               </tr>
            {/foreach}
        {/foreach}
        </table>
  
<div style="clear:both"></div> 


 <h2>{$LANG.onappdefaultfirewallrule}</h2>
 
        <form action="{$smarty.const.ONAPP_FILE_NAME}?page=firewallrules&id={$id}&action=set_defaults" method="post">
            <table cellspacing="0" cellpadding="10" border="0" width="50%">
                <tr>
                    <th>{$LANG.onappinterfaceup}</th>
                    <th>{$LANG.onappcommandup}</th>
                </tr>
                {foreach from=$networkinterfaces item=network_interface}
                <tr>
                    <td>{$network_interface->_label}</td>
                    <td>
                        <select id="commands"  name="network_interfaces[{$network_interface->_id}]" >
                            {foreach from=$commands item=command_item}
                                <option value="{$command_item}" {if $network_interface->_default_firewall_rule == $command_item}selected="true"{/if}>{$command_item}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>    
                {/foreach}
                
            </table>
               <div style="clear:both"></div> <br /> 
            <input type="submit" value="{$LANG.onappsave}" />
        </form>
        
<div style="clear:both"></div> <br />

<div class="f_right">
    <form action='{$smarty.const.ONAPP_FILE_NAME}?page=firewallrules&id={$id}&action=apply' method="post">
        <input type="submit" value="{$LANG.onappapplyfirewallrules}" />
    </form>
</div>
        
<div style="clear:both"></div>
<br /><br /><br />