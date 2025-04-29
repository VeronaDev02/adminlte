<?php

namespace App\Interfaces\Selfs;

interface PythonApiServiceInterface
{
    /**
     * Estabelece conexão com o servidor WebSocket Python
     * @param string $pdvIp IP do PDV
     * @return bool Status da conexão
     */
    public function connect(string $pdvIp): bool;

    /**
     * Envia comando de registro para o PDV
     * @param string $pdvIp IP do PDV
     * @return array Resposta do registro
     */
    public function registerPDV(string $pdvIp): array;

    /**
     * Processa mensagens recebidas do PDV
     * @param string $message Mensagem recebida
     * @return array Dados processados
     */
    public function processMessage(string $message): array;

    /**
     * Verifica status de conexão com o PDV
     * @param string $pdvIp IP do PDV
     * @return bool Status de conexão
     */
    public function checkPDVStatus(string $pdvIp): bool;
}