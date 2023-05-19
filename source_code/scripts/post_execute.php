<?php
/*********************************************************************************
 * This file is part of package GSync for Gmail.
 * 
 * Author : Variance InfoTech PVT LTD (http://www.varianceinfotech.com)
 * All rights (c) 2020 by Variance InfoTech PVT LTD
 *
 * This Version of GSync for Gmail is licensed software and may only be used in 
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * written consent of Variance InfoTech PVT LTD
 * 
 * You can contact via email at info@varianceinfotech.com
 * 
 ********************************************************************************/
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