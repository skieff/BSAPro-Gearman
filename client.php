<?php

require_once "Job.php";
require_once "Logger.php";

$logger = new Logger();
$client = new GearmanClient();
$client->addServer(); // by default host/port will be "localhost" & 4730

foreach((new FilesystemIterator('./hd')) as $filePath => $fileInfo) {
    /** @var SplFileInfo $fileInfo */

    if ($fileInfo->isFile() && $fileInfo->isReadable()) {

        $job = new Job(
            $filePath,
            realpath('./converted/') . DIRECTORY_SEPARATOR . $fileInfo->getBasename('.' . $fileInfo->getExtension()),
            Job::$defaultResolutionList
        );

        $logger->sendingJob($job);
        $client->doBackground('convert', json_encode($job));
    }

}