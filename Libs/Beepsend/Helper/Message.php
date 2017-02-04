<?php

namespace Beepsend\Helper;

/**
 * Helper for sending messages
 * @package Beepsend
 */
class Message
{
    /**
     * Array of messages that we will send through this helper
     * @var array
     */
    private $messages = array();

    /**
     * Create new message to send
     * @param int|string $to Number where we are sending message, for multiple recepiants use array (number1, number2)
     * @param int|array $from Number we are sending from or text
     * @param string $message Message we are sending
     * @param string $encoding Encoding of message UTF-8, ISO-8859-15 or Unicode
     * @param array $options Array of additional options. More info on: http://api.beepsend.com/docs.html#send-sms
     * @return array
     */
    public function message($to, $from, $message, $encoding = 'UTF-8', $options = array())
    {
        $data = array(
            'from' => $from,
            'to' => $to,
            'message' => mb_convert_encoding($message, $encoding, 'UTF-8'),
            'encoding' => $encoding
        );

        /* Merge additional options if we have */
        if (!empty($options)) {
            $data = array_merge($data, $options);
        }

        $this->messages = $data;
    }

    /**
     * Return all messages for sending
     * @return array
     */
    public function get()
    {
        return $this->messages;
    }

}