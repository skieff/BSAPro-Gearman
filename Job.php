<?php

class Job implements JsonSerializable {

    const FIELD_SOURCE = 'source';
    const FIELD_DESTINATION = 'destination';
    const FIELD_RESOLUTIONS = 'resolutions';

    const FHD = 'FHD';
    const WQXGA = 'WQXGA';

    static $defaultResolutionList = [
        [2560 , 1600, ['WQXGA']],
        [2560 , 1440, ['WQXGA']],
        [1920 , 1200, ['WQXGA']],
        [1920 , 1080, ['FHD', 'WQXGA']],
        [1600 , 1200, ['FHD', 'WQXGA']],
        [1680 , 1050, ['FHD', 'WQXGA']],
        [1600 , 900, ['FHD', 'WQXGA']],
        [1440 , 1080, ['FHD', 'WQXGA']],
        [1440 , 900, ['FHD', 'WQXGA']],
        [1280 , 720, ['FHD', 'WQXGA']],
    ];

    private $_source;
    private $_destination;
    /**
     * @var array
     */
    private $_resolutionList;
    /**
     * @var SplFileInfo
     */
    private $_sourceFileInfo;

    function __construct($source, $destination, array $resolutionList = array())
    {
        $this->_source = $source;
        $this->_destination = $destination;
        $this->_resolutionList = $resolutionList;
    }

    public function formatTargetFilePath($width, $height) {
        return $this->getDestination() . DIRECTORY_SEPARATOR . $this->_getSourceBaseName() . '-' . $width . 'x' .$height . '.' . $this->_getSourceExtension();
    }

    public static function fromArray(array $data) {
        $resolutions = isset($data[static::FIELD_RESOLUTIONS])   ? $data[static::FIELD_RESOLUTIONS] : '';

        return new static(
            isset($data[static::FIELD_SOURCE])      ? $data[static::FIELD_SOURCE]       : '',
            isset($data[static::FIELD_DESTINATION]) ? $data[static::FIELD_DESTINATION]  : '',
            is_array(($resolutions))                ? $resolutions                      : array()
        );
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            static::FIELD_SOURCE        => $this->_source,
            static::FIELD_DESTINATION   => $this->_destination,
            static::FIELD_RESOLUTIONS   => $this->_resolutionList,
        ];
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * @return array
     */
    public function getResolutionList()
    {
        return $this->_resolutionList;
    }

    /**
     * @return SplFileInfo
     */
    public function sourceFileInfo() {
        if (empty($this->_sourceFileInfo)) {
            clearstatcache();
            $this->_sourceFileInfo = new SplFileInfo($this->getSource());
        }

        return $this->_sourceFileInfo;
    }

    /**
     * @return SplFileInfo
     */
    public function destinationInfo() {
        clearstatcache();
        return new SplFileInfo($this->getDestination());
    }

    /**
     * @return string
     */
    private function _getSourceBaseName() {
        return $this->sourceFileInfo()->getBasename('.' . $this->_getSourceExtension());
    }

    /**
     * @return string
     */
    private function _getSourceExtension() {
        return $this->sourceFileInfo()->getExtension();
    }
}