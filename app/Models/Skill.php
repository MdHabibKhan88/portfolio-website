<?php

namespace App\Models;

class Skill extends PortfolioModel
{
    protected $table = 'skills';

    public function category()
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}
