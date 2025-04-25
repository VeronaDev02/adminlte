<?php

namespace App\View\Components;

use Illuminate\View\Component;
use InvalidArgumentException;

class SelfsVideo extends Component
{
    public $rtspUrl;
    public $quadrantIndex;
    public $pdvNome;

    public function __construct(
        string $rtspUrl, 
        int $quadrantIndex, 
        string $pdvNome = ''
    ) {
        if (empty($rtspUrl)) {
            throw new InvalidArgumentException("RTSP URL é obrigatória para o vídeo do quadrante {$quadrantIndex}.");
        }

        $this->rtspUrl = $rtspUrl;
        $this->quadrantIndex = $quadrantIndex;
        $this->pdvNome = $pdvNome;
    }

    public function getVideoId()
    {
        return "remoteVideo{$this->quadrantIndex}";
    }

    public function getStatusId()
    {
        return "status{$this->quadrantIndex}";
    }

    public function render()
    {
        return view('components.selfs-video');
    }
} 