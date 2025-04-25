<?php

namespace App\View\Components;

use Illuminate\View\Component;
use InvalidArgumentException;

class SelfsQuadrant extends Component
{
    public $pdvData;
    public $quadrantIndex;

    public function __construct(
        array $pdvData, 
        int $quadrantIndex
    ) {
        if (empty($pdvData)) {
            throw new InvalidArgumentException("Dados do PDV são obrigatórios.");
        }

        $this->pdvData = $pdvData;
        $this->quadrantIndex = $quadrantIndex;
    }

    public function getQuadrantId()
    {
        return "quadrant{$this->quadrantIndex}";
    }

    public function render()
    {
        return view('components.selfs-quadrant');
    }
}