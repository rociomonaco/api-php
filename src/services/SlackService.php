<?php
namespace src\Services;

class SlackService
{
    private $webhookUrl;
    private $channel;

    public function __construct($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
        $this->channel = "#alerts"; // default channel
    }

    public function setCurrentChannel($channel)
    {
        $this->channel = $channel;
    }


    public function sendNotification($data)
    {
        $data['channel'] = $this->channel;
        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        file_get_contents($this->webhookUrl, false, $context);
    }
}