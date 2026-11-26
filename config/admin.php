<?php

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GalleryItem;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Volunteer;

/*
 | Definitions that drive the generic admin CRUD screens.
 | Field types: text, email, url, date, number, textarea, select, boolean, image, file, document
 | "bilingual" => true creates two inputs: <name>_en and <name>_bn.
 | NOTE: only strings/arrays here (no closures) so that "config:cache" works.
 */
return [
    'resources' => [
        'projects' => [
            'model' => Project::class, 'table' => 'projects', 'label' => 'a_projects', 'dir' => 'projects',
            'title' => 'title_en', 'sub' => 'category', 'image' => 'image',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'bilingual' => true, 'required' => true, 'unique' => true],
                ['name' => 'short_description', 'type' => 'textarea', 'bilingual' => true, 'max' => 500],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 10000],
                ['name' => 'technologies', 'type' => 'text', 'hint' => 'hint_technologies'],
                ['name' => 'category', 'type' => 'text', 'max' => 100],
                ['name' => 'github_url', 'type' => 'url'],
                ['name' => 'live_url', 'type' => 'url'],
                ['name' => 'is_featured', 'type' => 'boolean'],
                ['name' => 'image', 'type' => 'image'],
            ],
        ],
        'skill-categories' => [
            'model' => SkillCategory::class, 'table' => 'skill_categories', 'label' => 'a_skill_categories',
            'title' => 'name_en', 'sub' => 'icon',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'bilingual' => true, 'required' => true, 'unique' => true],
                ['name' => 'icon', 'type' => 'text', 'hint' => 'hint_icon', 'max' => 100],
            ],
        ],
        'skills' => [
            'model' => Skill::class, 'table' => 'skills', 'label' => 'a_skills',
            'title' => 'name', 'sub' => 'level',
            'fields' => [
                ['name' => 'skill_category_id', 'type' => 'select', 'required' => true, 'options' => 'skill_categories'],
                ['name' => 'name', 'type' => 'text', 'required' => true, 'max' => 100, 'unique_scope' => 'skill_category_id'],
                ['name' => 'level', 'type' => 'number', 'min' => 0, 'max' => 100, 'hint' => 'hint_level'],
            ],
        ],
        'education' => [
            'model' => Education::class, 'table' => 'education', 'label' => 'a_education',
            'title' => 'institution_en', 'sub' => 'degree_en',
            'fields' => [
                ['name' => 'institution', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'degree', 'type' => 'text', 'bilingual' => true],
                ['name' => 'field', 'type' => 'text', 'bilingual' => true],
                ['name' => 'start_year', 'type' => 'text', 'max' => 20],
                ['name' => 'end_year', 'type' => 'text', 'max' => 20],
                ['name' => 'is_current', 'type' => 'boolean'],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 3000],
            ],
        ],
        'experience' => [
            'model' => Experience::class, 'table' => 'experiences', 'label' => 'a_experience',
            'title' => 'title_en', 'sub' => 'organization_en',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'organization', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'location', 'type' => 'text'],
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date'],
                ['name' => 'is_current', 'type' => 'boolean'],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 3000],
            ],
        ],
        'volunteers' => [
            'model' => Volunteer::class, 'table' => 'volunteer_experiences', 'label' => 'a_volunteers', 'dir' => 'volunteers',
            'title' => 'organization_en', 'sub' => 'role_en', 'image' => 'image',
            'fields' => [
                ['name' => 'organization', 'type' => 'text', 'bilingual' => true, 'required' => true, 'unique' => true],
                ['name' => 'role', 'type' => 'text', 'bilingual' => true],
                ['name' => 'period', 'type' => 'text', 'hint' => 'hint_period', 'max' => 100],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 3000],
                ['name' => 'image', 'type' => 'image'],
            ],
        ],
        'certificates' => [
            'model' => Certificate::class, 'table' => 'certificates', 'label' => 'a_certificates', 'dir' => 'certificates',
            'title' => 'title_en', 'sub' => 'issuer_en',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'issuer', 'type' => 'text', 'bilingual' => true],
                ['name' => 'issue_date', 'type' => 'date'],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 3000],
                ['name' => 'file', 'type' => 'file'],
            ],
        ],
        'achievements' => [
            'model' => Achievement::class, 'table' => 'achievements', 'label' => 'a_achievements',
            'title' => 'title_en', 'sub' => 'achieved_on',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'max' => 3000],
                ['name' => 'achieved_on', 'type' => 'date'],
            ],
        ],
        'gallery' => [
            'model' => GalleryItem::class, 'table' => 'gallery_items', 'label' => 'a_gallery', 'dir' => 'gallery',
            'title' => 'title_en', 'sub' => 'caption_en', 'image' => 'image',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'caption', 'type' => 'text', 'bilingual' => true],
                ['name' => 'image', 'type' => 'image'],
            ],
        ],
        'social-links' => [
            'model' => SocialLink::class, 'table' => 'social_links', 'label' => 'a_social_links',
            'title' => 'platform', 'sub' => 'url',
            'fields' => [
                ['name' => 'platform', 'type' => 'text', 'required' => true, 'max' => 50],
                ['name' => 'url', 'type' => 'url', 'required' => true],
                ['name' => 'icon', 'type' => 'text', 'hint' => 'hint_icon', 'max' => 100],
            ],
        ],
    ],

    // Single-record pages stored on the profile row.
    'profile_sections' => [
        'profile' => [
            'label' => 'a_profile', 'dir' => 'profile',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'bilingual' => true, 'required' => true],
                ['name' => 'headline', 'type' => 'text', 'bilingual' => true],
                ['name' => 'intro', 'type' => 'textarea', 'bilingual' => true, 'max' => 2000],
                ['name' => 'university', 'type' => 'text', 'bilingual' => true],
                ['name' => 'department', 'type' => 'text', 'bilingual' => true],
                ['name' => 'location', 'type' => 'text', 'bilingual' => true],
                ['name' => 'email', 'type' => 'email'],
                ['name' => 'phone', 'type' => 'text', 'max' => 40],
                ['name' => 'photo', 'type' => 'image'],
            ],
        ],
        'about' => [
            'label' => 'a_about', 'dir' => 'profile',
            'fields' => [
                ['name' => 'about', 'type' => 'textarea', 'bilingual' => true, 'max' => 5000],
                ['name' => 'academic', 'type' => 'textarea', 'bilingual' => true, 'max' => 5000],
                ['name' => 'interests', 'type' => 'textarea', 'bilingual' => true, 'max' => 5000],
                ['name' => 'objectives', 'type' => 'textarea', 'bilingual' => true, 'max' => 5000],
            ],
        ],
        'cv' => [
            'label' => 'a_cv', 'dir' => 'cv',
            'fields' => [
                ['name' => 'cv_file', 'type' => 'document'],
            ],
        ],
    ],

    'settings_fields' => [
        ['name' => 'site_title', 'type' => 'text', 'bilingual' => true, 'max' => 150],
        ['name' => 'meta_description', 'type' => 'textarea', 'bilingual' => true, 'max' => 300],
        ['name' => 'footer_tagline', 'type' => 'text', 'bilingual' => true, 'max' => 200],
    ],
];
