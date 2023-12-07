<?php
 
global $sugar_config;
$databaseType = $sugar_config['dbconfig']['db_type'];

if($databaseType == 'mysql'){

    //gsync gmail config table
    $gsyncGmailConfig = "CREATE TABLE IF NOT EXISTS vi_gsync_gmail_config(active_gsync_gmail TINYINT(1))";
    $gsyncGmailConfigResult = $GLOBALS['db']->query($gsyncGmailConfig); 
}else if($databaseType == 'mssql'){

    //multiple fields config table
    $gsyncGmailConfig = "IF NOT EXISTS (SELECT * FROM dbo.sysobjects where id = object_id(N'dbo.[vi_gsync_gmail_config]') and OBJECTPROPERTY(id, N'IsTable') = 1)
                    BEGIN

                    CREATE TABLE [dbo].[vi_gsync_gmail_config](
                        [active_gsync_gmail] [SMALLINT] NOT NULL
                    )
                    END";
    $gsyncGmailConfigResult = $GLOBALS['db']->query($gsyncGmailConfig);    
}//end of else if