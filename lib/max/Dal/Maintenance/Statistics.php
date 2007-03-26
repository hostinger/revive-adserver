<?php

/*
+---------------------------------------------------------------------------+
| Max Media Manager v0.3                                                    |
| =================                                                         |
|                                                                           |
| Copyright (c) 2003-2006 m3 Media Services Limited                         |
| For contact details, see: http://www.m3.net/                              |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/lib/max/OperationInterval.php';
require_once MAX_PATH . '/lib/max/core/ServiceLocator.php';
require_once MAX_PATH . '/lib/max/Dal/Maintenance/Common.php';
require_once 'Date.php';

/**
 * The non-DB specific Data Access Layer (DAL) class for the Maintenance Statistics Engine.
 *
 * @package    MaxDal
 * @subpackage MaintenanceStatistics
 * @author     James Floyd <james@m3.net>
 * @author     Andrew Hill <andrew@m3.net>
 * @author     Radek Maciaszek <radek@m3.net>
 */
class MAX_Dal_Maintenance_Statistics extends MAX_Dal_Maintenance_Common
{

    /**
     * The class constructor method.
     */
    function MAX_Dal_Maintenance_Statistics()
    {
        parent::MAX_Dal_Maintenance_Common();
    }

    /**
     * A method to store the a maintenance satistics run report.
     *
     * @param String $report The report to be logged.
     */
    function setMaintenanceStatisticsRunReport($report)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $query = "
            INSERT INTO
                {$conf['table']['prefix']}{$conf['table']['userlog']}
                (
                    timestamp,
                    usertype,
                    userid,
                    action,
                    object,
                    details
                )
            VALUES
                (
                    '".time()."',
                    '".phpAds_userMaintenance."',
                    0,
                    '".phpAds_actionBatchStatistics."',
                    0,
                    '".addslashes($report)."'
                )";
        MAX::debug('Logging the maintenance statistics run report', PEAR_LOG_DEBUG);
        return $this->oDbh->exec($query);
    }

    /**
     * A method to store details on the last time that the maintenance satistics
     * process ran.
     *
     * @param Date $oStart The time that the maintenance statistics run started.
     * @param Date $oEnd The time that the maintenance statistics run ended.
     * @param Date $oUpdateTo The end of the last operation interval ID that
     *                        has been updated.
     * @param string $runTypeField Name of DB field to hold $type value.
     *                      currently 'adserver_run_type' or 'tracker_run_type'.
     * @param integer $type The type of statistics run performed.
     */
    function setMaintenanceStatisticsLastRunInfo($oStart, $oEnd, $oUpdateTo, $runTypeField, $type)
    {
        if (empty($runTypeField)) {
            return PEAR::raiseError('$runTypeField parameter requires a value.', MAX_ERROR_INVALIDARGS);
        }
        return $this->setProcessLastRunInfo($oStart, $oEnd, $oUpdateTo, 'log_maintenance_statistics', false, $runTypeField, $type);
    }

}

?>
