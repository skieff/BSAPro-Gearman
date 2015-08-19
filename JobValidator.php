<?php

require_once "Job.php";
require_once "Logger.php";

class JobValidator {
    /**
     * @var Logger
     */
    private $_logger;

    function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }


    public function isValid(Job $job) {
        return $this->_isSourceReadable($job) && $this->_isDestinationDirWritable($job);
    }

    private function _isSourceReadable(Job $job) {
        if (!$job->sourceFileInfo()->isReadable()) {
            $this->_logger->sourceIsNotReadable($job);
            return false;
        }

        return true;
    }

    private function _isDestinationDirWritable(Job $job) {
        if (!$job->destinationInfo()->isDir()) {
            $this->_logger->goingToCreateDestinationDir($job);

            if (false == mkdir($job->getDestination())) {
                $this->_logger->cannotCreateDestinationDir($job);
                return false;
            }
        }

        if (!$job->destinationInfo()->isWritable()) {
            $this->_logger->destinationDirIsNotWritable($job);
            return false;
        }

        return true;
    }
}