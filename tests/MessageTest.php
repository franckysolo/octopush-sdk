<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Octopush\Message;

class MessageTest extends TestCase
{
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @return \Octopush\Message
     */
    public function testCreateInstanceOfMessageWithoutParams()
    {
        $message = new Message();
    }

    /**
     * @test
     * @return \Octopush\Message
     */
    public function testCreateInstanceOfMessageWithRequiredParams()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message simple sms',
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::SIMULATION_MODE,
          'sms_mode' => Message::NO_DELAY,
          'msisdn_sender' => 0
        ]);
        $this->assertInstanceOf(Message::class, $message);
        return $message;
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The sms text is too long
     * @return void
     */
    public function testCreateMessageWithTooLongText()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => str_repeat('0', 600),
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => 'Octopush sdk'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The sms type EU is invalid
     * @return void
     */
    public function testCreateInstanceOfMessageWithInvalidType()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message simple sms',
          'sms_type' => 'EU',
          'sms_sender' => 'Octopush sdk'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The sms sender $$$$___AKR_____ is invalid
     * @return void
     */
    public function testCreateInstanceOfMessageWithInvalidSender()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message simple sms',
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => '$$$$___AKR_____'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The request mode xxx is not supported, real or simu expected!
     * @return void
     */
    public function testCreateInstanceOfMessageWithInvalidRequestMode()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message simple sms',
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => 'xxx'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The sms mode 3 is not supported, 1 or 2 expected!
     * @return void
     */
    public function testCreateInstanceOfMessageWithInvalidSmsMode()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'sms_mode' => 3
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage This Msisdn 7 is not supported 0 or 1 expected!
     * @return void
     */
    public function testCreateInstanceOfMessageWithInvalidMsisdnSender()
    {
        new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'sms_mode' =>  Message::NO_DELAY,
          'msisdn_sender' => 7
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function testCreateMessagePremium()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'sms_mode' => Message::NO_DELAY
        ]);
        // sending_date is define by default
        $this->assertCount(7, $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateDeferMessage()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'sms_mode' => Message::WITH_DELAY,
          'sending_date' => (new \DateTime('now'))->modify('+1 year')
        ]);
        // sending_time is define by default
        // force it
        $message->setSendingTime(new \DateTime('now'));
        $message->setSendingPeriod((new \DateTime('now'))->modify('+10 minutes'));
        $this->assertArrayHasKey('sms_mode', $message->getParams());
        $this->assertArrayHasKey('sending_date', $message->getParams());
        $this->assertArrayHasKey('sending_time', $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateMessageWithReply()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'with_replies' => 1
        ]);

        $this->assertArrayHasKey('with_replies', $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateTransactionalMessage()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'transactional' => 1
        ]);

        $this->assertArrayHasKey('transactional', $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreatePublipostageMessage()
    {
        $text = '{ch1} {nom} {prenom}, your session begin at {ch2} the {ch3}';
        $message = new Message([
          'sms_recipients' => [TEST_PHONE_NUMBER, TEST_PHONE_NUMBER],
          'sms_text' => $text,
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::SIMULATION_MODE,
          'recipients_first_names' => ['John', 'Jane'],
          'recipients_last_names' => ['John', 'Jane'],
          'sms_fields_1' => ['Mr', 'Miss'],
          'sms_fields_2' => ['08:00 am', '01:00 pm'],
          'sms_fields_3' => ['2018/05/21', '2018/05/22'],
        ]);

        $this->assertArrayHasKey('recipients_first_names', $message->getParams());
        $this->assertArrayHasKey('recipients_last_names', $message->getParams());
        $this->assertArrayHasKey('sms_fields_1', $message->getParams());
        $this->assertArrayHasKey('sms_fields_2', $message->getParams());
        $this->assertArrayHasKey('sms_fields_3', $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateMessageWithRequestKeys()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'request_id' => uniqid(),
          'request_keys' => 'TRYS',
        ]);

        $this->assertArrayHasKey('request_sha1', $message->getParams());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateMessageWithUnvalidRequestKeys()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'request_id' => uniqid(),
          'request_keys' => 'TRYS$',
        ]);

        $this->assertArrayHasKey('request_sha1', $message->getParams());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required params for Octopush message
     * @return void
     */
    public function testCreateMessageWithUnvalidParams()
    {
        $message = new Message([
          'sms_recipients' => ['06000000'],
          'sms_text' => 'Message premium sms',
          'sms_type' => Message::SMS_PREMIUM,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::REAL_MODE,
          'request_id' => uniqid(),
          'request_key' => 'TRYS',
        ]);
    }
}
