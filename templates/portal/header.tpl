<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset={$charset}" />
<title>{$companyname} - {$pagetitle}{if $kbarticle.title} - {$kbarticle.title}{/if}</title>
{if $systemurl}<base href="{$systemurl}" />{/if}
<link rel="stylesheet" type="text/css" href="templates/{$template}/style.css" />
<script type="text/javascript" src="includes/jscript/jquery.js"></script>
</head>
<body>
<div id="top_container">
  <div id="top">
    <div id="company_title">{$companyname}</div>
    <div id="welcome_box">{if $loggedin}{$LANG.welcomeback}, <strong>{$loggedinuser.firstname}</strong>&nbsp;&nbsp;&nbsp;<img src="templates/{$template}/images/icons/details.gif" alt="{$LANG.clientareanavdetails}" width="16" height="16" border="0" class="absmiddle" /> <a href="clientarea.php?action=details" title="{$LANG.clientareanavdetails}"><strong>{$LANG.clientareanavdetails}</strong></a>&nbsp;&nbsp;&nbsp;<img src="templates/{$template}/images/icons/logout.gif" alt="{$LANG.logouttitle}" width="16" height="16" border="0" class="absmiddle" /> <a href="logout.php" title="Logout"><strong>{$LANG.logouttitle}</strong></a>{else}{$LANG.please} <a href="clientarea.php" title="{$LANG.loginbutton}"><strong>{$LANG.loginbutton}</strong></a> {$LANG.or} <a href="register.php" title="{$LANG.clientregistertitle}"><strong>{$LANG.clientregistertitle}</strong></a>{/if}</div>
  </div>
</div>
<div id="content_container">
{if $loggedin}
  <div id="top_menu">
    <ul>
      <li><a href="clientarea.php" title="{$LANG.clientareanavhome}">{$LANG.clientareanavhome}</a></li>
      <li><a href="clientarea.php?action=details" title="{$LANG.clientareanavdetails}">{$LANG.clientareanavdetails}</a></li>
      <li><a href="clientarea.php?action=products" title="{$LANG.clientareanavservices}">{$LANG.clientareanavservices}</a></li>
      <li><a href="clientarea.php?action=domains" title="{$LANG.clientareanavdomains}">{$LANG.clientareanavdomains}</a></li>
      <li><a href="clientarea.php?action=invoices" title="{$LANG.invoices}">{$LANG.invoices}</a></li>
      <li><a href="supporttickets.php" title="{$LANG.clientareanavsupporttickets}">{$LANG.clientareanavsupporttickets}</a></li>
      <li><a href="affiliates.php" title="{$LANG.affiliatestitle}">{$LANG.affiliatestitle}</a></li>
      <li><a href="clientarea.php?action=emails" title="{$LANG.clientareaemails}">{$LANG.clientareaemails}</a></li>
      <li><a href="onapp.php" title="OnApp">OnApp</a></li>
    </ul>
    <div class="clear"></div>
  </div>
{/if}
  <div id="content_left">
    <h1>{$pagetitle}</h1>
	<p class="breadcrumb">{$breadcrumbnav}</p>
