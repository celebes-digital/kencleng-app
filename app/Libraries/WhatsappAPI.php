<?php

namespace App\Libraries;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class WhatsappAPI
{
    protected PendingRequest $api;
    protected TemplatePesan $templatePesan;
    protected string $whatsappNumber;
    protected string $apiToken;
    protected string $apiUrl;

    public int $delay       = 10;
    public ?int $schedule   = null;
    public ?string $pesan   = null;

    public function __construct(string $whatsappNumber)
    {
        $this->whatsappNumber = $whatsappNumber;
        $this->initialize();
    }

    protected function initialize()
    {
        $this->templatePesan  = new TemplatePesan();
        $this->apiToken       = env('API_TOKEN', '8b93c2d9-48f5-4a5f-9a13-f57c9626037d');
        $this->apiUrl         = env('API_URL', 'https://api.starsender.online/api/send');
        $this->api            = $this->setup();
    }

    protected function setup(): PendingRequest
    {
        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => $this->apiToken,
        ];

        return Http::withHeaders($headers);
    }

    protected function prepareData(string $pesan = null): array
    {
        return [
            "messageType"   => "text",
            "to"            => $this->whatsappNumber,
            "delay"         => $this->delay,
            "schedule"      => $this->schedule,
            "body"          => $pesan ?? $this->pesan,
        ];
    }

    protected function makeApiCall(array $data)
    {
        return $this->api->post($this->apiUrl, $data);
    }

    protected function getTemplateMethod(string $type): ?string
    {
        $method = ucfirst($type);

        return method_exists($this->templatePesan, $method) ? $method : null;
    }

    public function getTemplateMessage(string $name, ?array $data): void
    {
        $method = $this->getTemplateMethod($name);

        if ($method) {
            $this->pesan = $this->templatePesan->$method($data);
        }
    }

    public function setSchedule(int $schedule, string $pesan = null)
    {
        $this->schedule = $schedule;
        $this->pesan    = $pesan ?? $this->pesan;

        return $this->send();
    }

    public function send(string $pesan = null)
    {
        $data   = $this->prepareData($pesan);
        $res    = $this->makeApiCall($data);

        $this->pesan    = null;
        $this->schedule = null;

        return $res->json();
    }
}