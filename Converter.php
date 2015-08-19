<?php
require_once "Job.php";
require_once "Logger.php";
require_once "JobValidator.php";

class Converter {
    const FAILED = 'failed';

    /**
     * @var JobValidator
     */
    private $_validator;
    /**
     * @var Logger
     */
    private $_logger;

    function __construct(JobValidator $validator, Logger $logger)
    {
        $this->_validator = $validator;
        $this->_logger = $logger;
    }

    public static function defaultConverter() {
        $logger = new Logger();
        $validator = new JobValidator($logger);

        return new static($validator, $logger);
    }

    public function doConvert(GearmanJob $job) {
        $workload = Job::fromArray(json_decode($job->workload(), true));

        $this->_logger->gotJob($workload);

        if (!$this->_validator->isValid($workload)) {
            return static::FAILED;
        }

        foreach($workload->getResolutionList() as list($width, $height, $applicableTo)) {
            $image = new Imagick($workload->sourceFileInfo()->getRealPath());

            if (!in_array($this->_getSourceType($image),$applicableTo )) {
                continue;
            }

            $image->resizeImage($width, $height, Imagick::FILTER_CATROM, 1, true);
            $image->writeImage($workload->formatTargetFilePath($width, $height));
        }

//    unlink($workload->sourceFileInfo()->getRealPath());

        return json_encode($workload);
    }

    private function _getSourceType(Imagick $image) {
        if ($image->getImageWidth() > 1920 && $image->getImageHeight() > 1080) {
            return Job::WQXGA;
        }

        return Job::FHD;
    }
}