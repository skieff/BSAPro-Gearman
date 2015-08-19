<?php

require_once "Job.php";

class Logger {

    public function waitingForJob() {
        echo "Waiting for job...\n";
    }

    public function sendingJob(Job $job) {
        echo "Sending convert job for" . $job->getSource() . "\n";
    }

    public function gotJob(Job $job) {
        echo "Source: " . var_export($job, true) . "\n";
    }

    public function sourceIsNotReadable(Job $job) {
        echo "Cannon read " . $job->getSource() . "\n";
    }

    public function goingToCreateDestinationDir(Job $job) {
        echo "Trying to create " . $job->getDestination() . "\n";
    }

    public function cannotCreateDestinationDir(Job $job) {
        echo "Cannon create output path " . $job->destinationInfo()->getRealPath() . "\n";
    }

    public function destinationDirIsNotWritable(Job $job) {
        echo "Cannon write to " . $job->destinationInfo()->getRealPath() . "\n";
    }
}