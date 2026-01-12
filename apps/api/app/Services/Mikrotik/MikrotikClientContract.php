<?php

namespace App\Services\Mikrotik;

interface MikrotikClientContract
{
    public function getPppActiveByName(string $name): ?array;
    public function ping(string $address, int $count = 5, int $intervalSeconds = 1): array;
    public function driverName(): string;

    public function setPppSecretRateLimit(string $pppoeName, ?string $rateLimit): void;
    public function setPppSecretDisabled(string $pppoeName, bool $disabled): void;

    public function setSimpleQueueMaxLimit(string $queueName, ?string $maxLimit): void;
    public function setSimpleQueueDisabled(string $queueName, bool $disabled): void;

    // NEW: create queue if missing (idempotent)
    public function ensureSimpleQueue(string $queueName, string $targetCidr, ?string $maxLimit): void;

    public function systemIdentity(): array;
}