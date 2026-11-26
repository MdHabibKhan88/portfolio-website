<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Adds the columns every orderable content table shares. */
    private function common(Blueprint $table): void
    {
        $table->unsignedInteger('sort_order')->default(0)->index();
        $table->boolean('is_active')->default(true)->index();
        $table->timestamps();
    }

    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            foreach (['name', 'headline', 'university', 'department', 'location'] as $col) {
                $table->string($col.'_en')->nullable();
                $table->string($col.'_bn')->nullable();
            }
            foreach (['intro', 'about', 'academic', 'interests', 'objectives'] as $col) {
                $table->text($col.'_en')->nullable();
                $table->text($col.'_bn')->nullable();
            }
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('photo')->nullable();
            $table->string('cv_file')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value_en')->nullable();
            $table->text('value_bn')->nullable();
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('en')->nullable();
            $table->text('bn')->nullable();
            $table->timestamps();
        });

        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_bn')->nullable();
            $table->string('icon', 100)->nullable();
            $this->common($table);
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained('skill_categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedTinyInteger('level')->nullable();
            $this->common($table);
            $table->unique(['skill_category_id', 'name']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_en')->unique();
            $table->string('title_bn')->nullable();
            $table->text('short_description_en')->nullable();
            $table->text('short_description_bn')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->string('technologies')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('live_url', 500)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $this->common($table);
        });

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            foreach (['institution', 'degree', 'field'] as $col) {
                $table->string($col.'_en')->nullable();
                $table->string($col.'_bn')->nullable();
            }
            $table->string('start_year', 20)->nullable();
            $table->string('end_year', 20)->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $this->common($table);
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            foreach (['title', 'organization'] as $col) {
                $table->string($col.'_en')->nullable();
                $table->string($col.'_bn')->nullable();
            }
            $table->string('location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $this->common($table);
        });

        Schema::create('volunteer_experiences', function (Blueprint $table) {
            $table->id();
            $table->string('organization_en')->unique();
            $table->string('organization_bn')->nullable();
            $table->string('role_en')->nullable();
            $table->string('role_bn')->nullable();
            $table->string('period', 100)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->string('image')->nullable();
            $this->common($table);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            foreach (['title', 'issuer'] as $col) {
                $table->string($col.'_en')->nullable();
                $table->string($col.'_bn')->nullable();
            }
            $table->date('issue_date')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->string('file')->nullable();
            $this->common($table);
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_bn')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->date('achieved_on')->nullable();
            $this->common($table);
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_bn')->nullable();
            $table->string('caption_en')->nullable();
            $table->string('caption_bn')->nullable();
            $table->string('image')->nullable();
            $this->common($table);
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50);
            $table->string('url', 500);
            $table->string('icon', 100)->nullable();
            $this->common($table);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('subject', 150);
            $table->text('message');
            $table->boolean('is_read')->default(false)->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'activities', 'contact_messages', 'social_links', 'gallery_items', 'achievements', 'certificates',
            'volunteer_experiences', 'experiences', 'education', 'projects', 'skills', 'skill_categories',
            'translations', 'settings', 'profiles',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
