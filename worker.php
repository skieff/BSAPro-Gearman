<?php
require_once "Converter.php";

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction("convert", array(Converter::defaultConverter(), 'doConvert'));

$logger = new Logger();

while (1) {
    $logger->waitingForJob();
    $ret = $worker->work(); // work() will block execution until a job is delivered
    if ($worker->returnCode() != GEARMAN_SUCCESS) {
        break;
    }
}