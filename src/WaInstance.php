<?php

namespace Jorrmaglione\Waapi;

/**
 *
 */
final class WaInstance {
    /**
     * @var int
     */
    private int $id;
    /**
     * @var WaApi
     */
    private WaApi $api;
    /**
     * @var WaInstanceClient|null
     */
    private ?WaInstanceClient $client;

    /**
     * @param WaApi  $api
     * @param int    $id
     * @param string $owner
     * @param string $name
     */
    public function __construct(WaApi $api, int $id) {
        $this->api = $api;
        $this->id = $id;
        $this->client = null;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return WaApi
     */
    public function getApi(): WaApi {
        return $this->api;
    }

    /**
     * @return WaInstanceClient
     */
    public function getClient(): WaInstanceClient {
        return $this->client ??= new WaInstanceClient($this);
    }
}