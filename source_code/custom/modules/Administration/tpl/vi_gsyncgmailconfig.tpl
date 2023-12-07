{*
 
*}
<html>
    <head>
        {if $THEME eq  'SuiteP'}
            <link rel="stylesheet" type="text/css" href="custom/modules/Administration/css/VIGsyncGmail.css">
        {else}
            <link rel="stylesheet" type="text/css" href="custom/modules/Administration/css/VIGsyncGmailSuite7R.css">
        {/if}
    </head>
    <div class="moduleTitle">
        <h2 class="module-title-text">{$MOD.LBL_GSYNC_GMAIL}</h2>
        <div class="clear"></div>
    </div><br>
    {$HELP_BOX_CONTENT}
    <div style="float: right;">
        <a href="index.php?module=VIGsyncGmailLicenseAddon&action=license"><button class="button">{$MOD.LBL_UPDATE_LICENSE}</button></a>
    </div>
    <span style="margin-left: 14px;">
        <b>{$MOD.LBL_ACTIVE_GSYNC_GMAIL_FEATURE}</b>
    </span>
    <label class="switch">
        <input type="checkbox" id="active_gsync_gmail" name="active_gsync_gmail" value="{$ACTIVE_GSYNC_GMAIL}" {if  $ACTIVE_GSYNC_GMAIL eq '1'} checked{/if}>
        <span class="slider round" style="margin-left: 22px;margin-top: 14px;"></span>
    </label><br><br>
</html>
{literal}
    <script type="text/javascript">
        var mod = {/literal}{$MOD|@json_encode}{literal};
        var script   = document.createElement("script");
        script.type  = "text/javascript";
        script.src   = "custom/modules/Administration/js/VIGsyncGmailConfiguration.js?v="+Math.random();
        document.body.appendChild(script);
    </script>
{/literal}