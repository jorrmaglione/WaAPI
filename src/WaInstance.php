<?php

namespace Jorrmaglione\Waapi;

use InvalidArgumentException;
use RuntimeException;

/**
 *
 */
final class WaInstance {
    /**
     * @var int
     */
    private int $instanceId;
    /**
     * @var WaApi
     */
    private WaApi $api;

    /**
     * @param WaApi  $api
     * @param int    $instanceId
     * @param string $owner
     * @param string $name
     */
    public function __construct(WaApi $api, int $instanceId) {
        $this->api = $api;
        $this->instanceId = $instanceId;
    }

    /**
     * @return int
     */
    public function getInstanceId(): int {
        return $this->instanceId;
    }

    /**
     * @return WaApi
     */
    public function getApi(): WaApi {
        return $this->api;
    }

    /**
     * @return array
     */
    public function getStatus(): array {
        return $this->api->request('GET', "instances/$this->instanceId/client/status");
    }

    public function isReady(): bool {
        return $this->getStatus()['clientStatus']["instanceStatus"] === 'ready';
    }

    public function instanceStatus(): array {
        $res = $this->api->request('GET', "instances/$this->instanceId/client/status");

        if (isset($res['status']) && $res['status'] !== 'success')
            return [];

        if (empty($res['clientStatus']))
            return [];

        return $res['clientStatus'];
    }

    public function retrieveQRCode(): void {
        $res = $this->api->request('GET', "instances/$this->instanceId/client/qr");
        var_dump($res);
    }

    /**
     * @param string $numberE164
     *
     * @return string
     */
    public function getFormattedNumber(string $numberE164): string {
        $res = $this->api->request('POST', "instances/{$this->instanceId}/client/action/get-formatted-number", [
            'number' => $numberE164,
        ]);
        return $res['data']['data']['formattedNumber'] ?? new RuntimeException('Formatted number not returned');
    }

    /**
     * @param string $numberE164
     *
     * @return string
     */
    public function getNumberId(string $numberE164): string {
        $res = $this->api->request('POST', "instances/{$this->instanceId}/client/action/get-number-id", [
            'number' => $numberE164,
        ]);
        return $res['data']['data']['numberId']['_serialized'] ?? throw new RuntimeException('Number ID not returned');
    }

    /**
     * @param string $chatId
     * @param string $message
     *
     * @return array
     */
    public function sendText(string $chatId, string $message): array {
        return $this->api->request('POST', "instances/{$this->instanceId}/client/action/send-message", [
            'chatId' => $chatId,
            'message' => $message,
        ]);
    }

    /**
     * @param string      $chatId
     * @param string      $filePath
     * @param string|null $filename
     * @param string|null $caption
     *
     * @return array
     */
    public function sendMediaBase64(string $chatId, string $filePath, ?string $filename = null, ?string $caption = null): array {
        if (!is_readable($filePath))
            throw new InvalidArgumentException("File not readable: $filePath");

        $bytes = file_get_contents($filePath);

        return $this->api->request('POST', "instances/{$this->instanceId}/client/action/send-media", [
            'chatId' => $chatId,
            'mediaBase64' => base64_encode($bytes),
            'mediaName' => $filename ?? basename($filePath),
            'caption' => $caption,
        ]);
    }

    /**
     * @param string      $chatId
     * @param string      $url
     * @param string|null $caption
     *
     * @return array
     */
    public function sendMediaUrl(string $chatId, string $url, ?string $caption = null): array {
        if (!filter_var($url, FILTER_VALIDATE_URL))
            throw new InvalidArgumentException("Invalid URL: $url");

        return $this->api->request('POST', "instances/{$this->instanceId}/client/action/send-media", [
            'chatId' => $chatId,
            'mediaUrl' => $url,
            'caption' => $caption,
        ]);
    }
}