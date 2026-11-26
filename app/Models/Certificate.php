<?php

namespace App\Models;

class Certificate extends PortfolioModel
{
    protected $table = 'certificates';

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['issue_date' => 'date']);
    }
}
