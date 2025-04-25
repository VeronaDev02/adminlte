<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelfsGrid extends Component
{
    public $pdvDataList;
    public $serverConfig;
    public $activeQuadrants;
    public $currentLayout;
    public $expansionDirection;
    public $cols;
    public $rows;

    public function __construct(
        $pdvDataList, 
        $serverConfig, 
        $activeQuadrants = null,
        $cols = null,
        $rows = null,
        $expansionDirection = 'horizontal',
        $layout = null
    ) {
        $this->pdvDataList = $pdvDataList;
        $this->serverConfig = $serverConfig;
        $this->expansionDirection = $expansionDirection;
        
        $totalPdvs = count($pdvDataList);
        
        $this->activeQuadrants = $activeQuadrants ?? ($totalPdvs % 2 === 0 ? $totalPdvs : $totalPdvs - 1);
        
        $this->activeQuadrants = max(2, min($this->activeQuadrants, $totalPdvs));
        
        if ($this->activeQuadrants % 2 !== 0) {
            $this->activeQuadrants = $this->activeQuadrants - 1;
        }
        
        $this->currentLayout = $layout ?? $this->calculateLayout();
        
        $this->cols = $cols ?? $this->currentLayout[0];
        $this->rows = $rows ?? count($this->currentLayout);
    }
    
    public function getResponsiveGridStyle()
    {
        return "
            /* Estilos responsivos para a grid */
            @media (max-width: 992px) {
                .stream-grid {
                    gap: 0.5px;
                }
            }
            @media (max-width: 768px) {
                .stream-container {
                    flex-direction: column;
                }
                .log-container, .video-container {
                    width: 100%;
                    height: 50%;
                }
            }
        ";
    }
    
    protected function calculateLayout()
    {
        if ($this->activeQuadrants == 2) {
            return $this->expansionDirection == 'horizontal' 
                ? [2]
                : [1, 1];
        }
        
        if ($this->activeQuadrants == 4) {
            return [2, 2];
        }
        
        if ($this->expansionDirection == 'horizontal') {
            return $this->calculateHorizontalLayout();
        } 
        else {
            return $this->calculateVerticalLayout();
        }
    }
    
    protected function calculateHorizontalLayout()
    {
        $quadrantsLeft = $this->activeQuadrants;
        $rows = 2;
        
        $colsPerRow = ceil($quadrantsLeft / $rows);
        
        if ($colsPerRow % 2 != 0) {
            $colsPerRow++;
        }
        
        $layout = [];
        for ($i = 0; $i < $rows; $i++) {
            $layout[] = $colsPerRow;
        }
        
        return $layout;
    }

    protected function calculateVerticalLayout()
    {
        $quadrantsLeft = $this->activeQuadrants;
        $cols = 2; 
        
        $rowsPerCol = ceil($quadrantsLeft / $cols);
        
        if ($rowsPerCol % 2 != 0) {
            $rowsPerCol++;
        }
        
        $layout = [];
        for ($i = 0; $i < $rowsPerCol; $i++) {
            $layout[] = $cols;
        }
        
        return $layout;
    }
    
    public function getQuadrantOptions()
    {
        $maxPdvs = count($this->pdvDataList);
        $options = [];
        
        for ($i = 2; $i <= $maxPdvs; $i += 2) {
            $options[] = $i;
        }
        
        return $options;
    }
    
    public function isValidLayout($layout)
    {
        $totalCells = array_sum($layout);
        
        return $totalCells == $this->activeQuadrants;
    }
    
    public function getGridClass()
    {
        if ($this->activeQuadrants == 2) {
            return $this->expansionDirection == 'horizontal' 
                ? 'selfs-grid-horizontal-2' 
                : 'selfs-grid-vertical-2';
        }
        
        return $this->expansionDirection == 'horizontal' 
            ? 'selfs-grid-horizontal' 
            : 'selfs-grid-vertical';
    }
    
    public function getLayoutDescription()
    {
        if ($this->expansionDirection == 'horizontal') {
            if (count(array_unique($this->currentLayout)) == 1) {
                $cols = $this->currentLayout[0];
                $rows = count($this->currentLayout);
                return "{$cols}x{$rows}"; // Ex: "3x2" (3 colunas, 2 linhas)
            }
        } else {
            if (count(array_unique($this->currentLayout)) == 1) {
                $cols = $this->currentLayout[0];
                $rows = count($this->currentLayout);
                return "{$cols}x{$rows}"; // Ex: "2x3" (2 colunas, 3 linhas)
            } else {
                return implode('x', $this->currentLayout); // Ex: "2x2x2" (3 linhas com 2 colunas cada)
            }
        }
        
        return implode('x', $this->currentLayout);
    }

    public function render()
    {
        return view('components.selfs-grid');
    }
}