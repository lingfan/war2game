<?php
/**
 * Defines globals used throughout amfphp package for config options
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage app
 */

global $amfphp;

$amfphp['errorLevel'] = E_ALL ^ E_NOTICE;
$amfphp['instanceName'] = NULL;
$amfphp['classPath'] = 'services/';
$amfphp['customMappingsPath'] = 'services/';
$amfphp['webServiceMethod'] = 'php5';
$amfphp['disableDescribeService'] = false;
$amfphp['disableTrace'] = false;
$amfphp['disableDebug'] = false;
$amfphp['lastMethodCall'] = '/1';
$amfphp['lastMessageId'] = '';
$amfphp['isFlashComm'] = false;
$amfphp['classInstances'] = array();
$amfphp['encoding'] = "amf3";
$amfphp['native'] = true;
$amfphp['totalTime'] = 0;
$amfphp['callTime'] = 0;
$amfphp['decodeTime'] = 0;
$amfphp['includeTime'] = 0;
//Because startTime is defined BEFORE this file is called, we don't define it here (else
//we would overwrite it

$amfphp['adapterMappings'] = array(
	'db_result' => 'peardb', //PEAR::DB
	'pdostatement' => 'pdo', //PDO
	'mysqli_result' => 'mysqliobject', //Mysqli
	'sqliteresult' => 'sqliteobject', //SQLite
	'sqliteunbuffered' => 'sqliteobject',
	'adorecordset' => 'adodb', //ADODB
	'zend_db_table_row' => 'zendrow', //Zend::DB
	'zend_db_table_rowset' => 'zendrowset',
	'recordset' => 'plainrecordset', //Plain recordset
	'doctrine_collection_immediate' => 'doctrine' //Doctrine table
);

?>