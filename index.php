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
        $pluginName = isset($metric->{'plugin'})===true?$metric->{'plugin'}:$metricName;
        $className = "CloudWatchScript\\Plugins\\" . $pluginName  . "Monitoring";
        $metricController = new $className($metric,  $metric->name);
        foreach ($metricController->getAlarms() as $key => $alarm) {
            $client->putMetricAlarm(array(
                'AlarmName' => $alarm["Name"],
                'AlarmDescription' => $metric->description,
                'ActionsEnabled' => true,
                'OKActions' => array($conf->alarms->action  ),
                'AlarmActions' => array($conf->alarms->action ),
                'InsufficientDataActions' => array($conf->alarms->action),
                'Dimensions' => array(
                    array('Name' => 'InstanceId', 'Value' => $instanceId),
                    array('Name' => 'Metrics', 'Value' => $metricName)
                ),
                'MetricName' => $metricController->getMetricName($alarm["Name"]),
                'Namespace' => $metric->namespace,
                'Statistic' => 'Average',
                'Period' => 300,
                'Unit' => $metricController->getUnit($alarm["Name"]),
                // EvaluationPeriods is required
                'EvaluationPeriods' => 2,
                // Threshold is required
                'Threshold' => $alarm["Threshold"],
                // ComparisonOperator is required
                'ComparisonOperator' => $alarm["ComparisonOperator"]
            ));
        }
    }
