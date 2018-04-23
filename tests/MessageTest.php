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
          'sms_mode' => Message::NO_DELAY
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
    public function testCreateMessageWithWithTooMuchChars()
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
}
