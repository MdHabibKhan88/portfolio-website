<?php

namespace App\Models;

class SkillCategory extends PortfolioModel
{
    protected $table = 'skill_categories';

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }
}
