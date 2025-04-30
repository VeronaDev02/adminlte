<?php

namespace App\Interfaces\Selfs;

interface PythonApiServiceInterface
{
    public function connect(string $pdvIp): bool;
    public function registerPDV(string $pdvIp): array;
    public function processMessage(string $message): array;
    public function checkPDVStatus(string $pdvIp): bool;
}