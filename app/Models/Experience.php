<?php

namespace App\Models;

class Experience extends PortfolioModel
{
    protected $table = 'experiences';

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['start_date' => 'date', 'end_date' => 'date']);
    }
}
