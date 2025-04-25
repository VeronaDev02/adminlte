<?php

namespace App\View\Components;

use Illuminate\View\Component;
use InvalidArgumentException;

class SelfsLog extends Component
{
    public $pdvIp;
    public $pdvNome;
    public $quadrantIndex;

    public function __construct(
        string $pdvIp, 
        int $quadrantIndex, 
        string $pdvNome = ''
    ) {
        if (empty($pdvIp)) {
            throw new InvalidArgumentException("PDV IP é obrigatório para o log do quadrante {$quadrantIndex}.");
        }

        $this->pdvIp = $pdvIp;
        $this->quadrantIndex = $quadrantIndex;
        $this->pdvNome = $pdvNome;
    }

    public function getLogContainerId()
    {
        return "log{$this->quadrantIndex}";
    }

    public function render()
    {
        return view('components.selfs-log');
    }
}