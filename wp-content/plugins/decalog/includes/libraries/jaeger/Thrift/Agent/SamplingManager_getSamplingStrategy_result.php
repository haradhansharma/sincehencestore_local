<?php
namespace Jaeger\Thrift\Agent;
/**
 * Autogenerated by Thrift Compiler (0.11.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;


class SamplingManager_getSamplingStrategy_result extends TBase {
  static $isValidate = false;

  static $_TSPEC = array(
    0 => array(
      'var' => 'success',
      'isRequired' => false,
      'type' => TType::STRUCT,
      'class' => '\Jaeger\Thrift\Agent\SamplingStrategyResponse',
      ),
    );

  /**
   * @var \Jaeger\Thrift\Agent\SamplingStrategyResponse
   */
  public $success = null;

  public function __construct($vals=null) {
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'SamplingManager_getSamplingStrategy_result';
  }

  public function read($input)
  {
    return $this->_read('SamplingManager_getSamplingStrategy_result', self::$_TSPEC, $input);
  }

  public function write($output) {
    return $this->_write('SamplingManager_getSamplingStrategy_result', self::$_TSPEC, $output);
  }

}

