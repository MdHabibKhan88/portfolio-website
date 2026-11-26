<?php

namespace App\Models;

class Achievement extends PortfolioModel
{
    protected $table = 'achievements';

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['achieved_on' => 'date']);
    }
}
