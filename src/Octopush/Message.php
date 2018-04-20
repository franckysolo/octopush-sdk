<?php

namespace Octopush;

/**
 * @author franckysolo <franckysolo@gmail.com>
 * @version 1.0
 * @package Octopush
 */
class Message
{
    /**
     * Characters count as double
     *
     * @var array
     */
    protected static $chars = [
      '{', '}', '€', '[', ']', '~', '^',
      '¨', '|', '&', '$', '@', '\n',
      '\r', '\t'
    ];

    /**
     * Required Parameters for api request with api_key & user_login
     *
     * @var array
     */
    protected $requiredKeys = [
      'sms_recipients',
      'sms_text',
      'sms_type',
      'sms_sender'
    ];

    /**
     * Optionnals Parameters for api request
     *
     * @var array
     */
    protected $optionalKeys = [
      'sms_mode',
      'sending_date',
      'sending_time',
      'sending_period',
      'recipients_first_names',
      'recipients_last_names',
      'sms_fields_1',
      'sms_fields_2',
      'sms_fields_3',
      'request_mode',
      'request_id',
      'transactional',
      'msisdn_sender',
      'request_keys',
      'request_sha1'
    ];

    public function __construct($options = [])
    {
    }
}
