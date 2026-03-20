<?php
$selfDir = dirname(__FILE__);

ini_set('max_execution_time', '60');
ini_set('default_socket_timeout', '60');

$locationOfVikonModules = '/'; //todo new_core_after del
$domenName = 'www.ntspi.ru';
$apiDomen = 'https://db-nica.ru/';
$vikonDomainResolveBypass = 'db-nica.ru:443:62.76.112.192';
$fmDomainResolveBypass = 'file.db-nica.ru:443:62.76.112.192';
$apiDomainAuth = 'https://auth.db-nica.ru/';
$filemanagerApiDomen = 'https://file.db-nica.ru/';
$clientId = '542';
$clientSecret = '0mmk80mv8zpk7h5uhjvjsmsskvpkn9nmkywgy83vwg7tpzdw0y7rao0k9pvmn8xxwj5mgw8554nhtq6s';
$vuzId = '16775';
$vuzName = 'Нижнетагильский государственный социально-педагогический институт (филиал) "Российского государственного профессионально-педагогического университета"';
$modulesByPathDeploy = array (
  2 => 'abitur',
  1 => 'sveden',
  6 => 'vsoko',
);
$allowedFoldersInCoreByModule = array (
  2 => 
  array (
    0 => 'abitur',
  ),
  1 => 
  array (
    0 => 'assets',
    1 => 'files_zaglushka',
    2 => 'common',
    3 => 'struct',
    4 => 'document',
    5 => 'education',
    6 => 'managers',
    7 => 'employees',
    8 => 'objects',
    9 => 'paid_edu',
    10 => 'budget',
    11 => 'vacant',
    12 => 'grants',
    13 => 'inter',
    14 => 'catering',
    15 => 'eduStandarts',
    16 => 'corruption',
    17 => 'antiterrorism',
    18 => 'files',
    19 => 'update',
    20 => 'index.html',
    21 => '.vikon',
    22 => '.htaccess',
  ),
  6 => 
  array (
    0 => 'assets',
    1 => 'general',
    2 => 'structure',
    4 => 'faq',
    5 => 'procedures',
    6 => 'results-and-reports',
    7 => 'plans',
    8 => 'survey',
    9 => 'files',
    10 => '.vikon',
    11 => 'index.html',
    12 => '.htaccess',
  ),
);

define('DEBUG_MODE', isset($_GET['debug_mode']) && $_GET['debug_mode'] || isset($_POST['debug_mode']) && $_POST['debug_mode']);

define('IS_DOMAIN_RESOLVE', isset($_COOKIE['is_resolve_domain']) ? (int) $_COOKIE['is_resolve_domain'] : 0);
define('FM_DOMAIN_RESOLVE_BYPASS', $fmDomainResolveBypass);
define('VIKON_DOMAIN_RESOLVE_BYPASS', $vikonDomainResolveBypass);

define('SVEDEN', 1);
define('ABITUR', 2);
define('VSOKO', 6);

if (DEBUG_MODE === true) {
    error_reporting(E_ALL);
    ini_set('display_errors','on');
} else {
    error_reporting(0);
}
