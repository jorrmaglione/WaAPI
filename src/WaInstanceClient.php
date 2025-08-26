<?php

namespace Jorrmaglione\Waapi;

use InvalidArgumentException;

/**
 *
 */
final class WaInstanceClient {
    /**
     * @var WaInstance
     */
    private WaInstance $instance;

    /**
     * @param WaInstance $instance
     */
    public function __construct(WaInstance $instance) {
        $this->instance = $instance;
    }

    /**
     * @return WaApi
     */
    private function getInstanceApi(): WaApi {
        return $this->instance->getApi();
    }

    /**
     * @return int
     */
    private function getInstanceId(): int {
        return $this->instance->getId();
    }

    /**
     * @return array
     */
    public function getStatus(): array {
        return $this->getInstanceApi()->request('GET', "instances/{$this->getInstanceId()}/client/status");
    }

    /**
     * @param string $phoneE164
     *
     * @return string
     */
    public function getFormattedNumber(string $phoneE164): string {
        $res = $this->getInstanceApi()->request('POST', "instances/{$this->getInstanceId()}/client/action/get-formatted-number", [
            'phone' => $phoneE164,
        ]);
        return $res['formattedNumber'] ?? $phoneE164;
    }

    /**
     * @param string $phoneE164
     *
     * @return string
     */
    public function getNumberId(string $phoneE164): string {
        $res = $this->getInstanceApi()->request('POST', "instances/{$this->getInstanceId()}/client/action/get-number-id", [
            'phone' => $phoneE164,
        ]);
        return $res['chatId'] ?? $res['id'] ?? throw new \RuntimeException('chatId not returned');
    }

    /**
     * @param string $chatId
     * @param string $message
     *
     * @return array
     */
    public function sendText(string $chatId, string $message): array {
        return $this->getInstanceApi()->request('POST', "instances/{$this->getInstanceId()}/client/action/send-message", [
            'chatId' => $chatId,
            'message' => $message,
        ]);
    }

    /**
     * @param string      $chatId
     * @param string      $filePath
     * @param string|null $caption
     *
     * @return array
     */
    public function sendMediaBase64(string $chatId, string $filePath, ?string $caption = null): array {
        if (!is_readable($filePath))
            throw new InvalidArgumentException("File not readable: $filePath");

        $bytes = file_get_contents($filePath);

        return $this->getInstanceApi()->request('POST', "instances/{$this->getInstanceId()}/client/action/send-media", [
            'chatId' => $chatId,
            'mediaBase64' => base64_encode($bytes),
            'mediaName' => basename($filePath),
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

        return $this->getInstanceApi()->request('POST', "instances/{$this->getInstanceId()}/client/action/send-media", [
            'chatId'   => $chatId,
            'mediaUrl' => $url,
            'caption'  => $caption,
        ]);
    }
}