<?php
error_reporting(E_ALL ^ E_NOTICE);

const TEMPLATE_PATH = 'Templates/';

require_once('TemplateEngine.php');
require_once('StatGenerator.php');
require_once('JsonFormatter.php');
require_once('TableFormatter.php');

$baseDir = isset($argv[1]) ? $argv[1] : getcwd();
$baseDir .= substr($baseDir, -1) == '/' ? '' : '/';
$dataDir = isset($argv[2]) ? $argv[2] : getcwd();
$dataDir .= substr($dataDir, -1) == '/' ? '' : '/';

$statisticsTemplate = new TemplateEngine('StatisticsTemplate');
$table = new TableFormatter();

$modules = array('.');

$startdate = '2006-01-01';

$generator = new StatGenerator($dataDir, $baseDir, $startdate);

list($persons, $commitCounts) = $generator->generateData($modules);
print_r($commitCounts);

$json = new JsonFormatter();
file_put_contents($dataDir . 'json.php', $json->generate($persons, $commitCounts, $generator->getMaxMonths()));

$statisticsTemplate->replaceMarkerArray(
	array (
		'last_update'	=> date('Y-m-d H:i', time()),
		'table'	=> $table->generate($persons, $commitCounts, $generator->getMaxMonths())
	)
);

file_put_contents(
	$dataDir . 'stat.html',
	$statisticsTemplate->getTemplate()
);