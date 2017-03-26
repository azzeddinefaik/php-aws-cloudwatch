<?php
include 'config.php';

$conf = (new lib\Config())->getConfigFile(__DIR__."/configs/conf.json");

if ($conf === false) {
    echo "Conf file is not valid";
    die();
}


// Store metric by namespace in order to call "AWS Could Watch" one time per namespace
$metricsToPush = array();


if($conf->aws->instance) {
    $instanceId = $conf->aws->instance;
}
else {
    echo "Conf file is not valid";
    die();
}

$client = (new lib\Config())->getCloudWatchClient($conf);

foreach ($conf->metrics as $metrics) {
    foreach ($metrics as $metricName => $metric) {
        /// WHAT TODO
    }
}
