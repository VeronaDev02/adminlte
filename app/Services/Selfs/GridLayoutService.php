<?php

namespace App\Services\Selfs;

class GridLayoutService
{
    public function generateQuadrantOptions(int $totalPdvs): array
    {
        $options = [];

        // Gera opções apenas para números pares e dentro do limite de PDVs
        for ($i = 2; $i <= 16; $i += 2) {
            if ($i <= $totalPdvs) {
                $options[] = $i;
            }
        }

        return $options;
    }

    public function generateLayoutPreviewHtml(int $quadrants, int $columns, int $rows): string
    {
        return view('user.selfs.layout-preview', [
            'quadrants' => $quadrants,
            'columns' => $columns,
            'rows' => $rows
        ])->render();
    }

    public function validateGridConfiguration(int $quadrants, int $columns): bool
    {
        // Verifica se o número de quadrantes é divisível pelo número de colunas
        return $quadrants % $columns === 0;
    }

    public function calculateRows(int $quadrants, int $columns): int
    {
        return $quadrants / $columns;
    }
}