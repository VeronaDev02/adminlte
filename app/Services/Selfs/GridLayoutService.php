<?php

namespace App\Services\Selfs;

class GridLayoutService
{
    /**
     * Gera opções de layout baseado no número total de PDVs
     * 
     * @param int $totalPdvs
     * @return array
     */
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

    /**
     * Gera o HTML do preview de layout
     * 
     * @param int $quadrants Número de quadrantes
     * @param int $columns Número de colunas
     * @param int $rows Número de linhas
     * @return string
     */
    public function generateLayoutPreviewHtml(int $quadrants, int $columns, int $rows): string
    {
        return view('user.selfs.layout-preview', [
            'quadrants' => $quadrants,
            'columns' => $columns,
            'rows' => $rows
        ])->render();
    }

    /**
     * Valida configuração de grid
     * 
     * @param int $quadrants
     * @param int $columns
     * @return bool
     */
    public function validateGridConfiguration(int $quadrants, int $columns): bool
    {
        // Verifica se o número de quadrantes é divisível pelo número de colunas
        return $quadrants % $columns === 0;
    }

    /**
     * Calcula número de linhas
     * 
     * @param int $quadrants
     * @param int $columns
     * @return int
     */
    public function calculateRows(int $quadrants, int $columns): int
    {
        return $quadrants / $columns;
    }
}