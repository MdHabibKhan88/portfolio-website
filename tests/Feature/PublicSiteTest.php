<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_works_with_an_empty_database(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_home_page_shows_seeded_content(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->get('/')->assertOk()
            ->assertSee('MD. HABIB KHAN')
            ->assertSee('Pneumonia Detection System')
            ->assertSee('KYAF Food Donation Management System')
            ->assertSee('Student Registration System')
            ->assertSee('Bangladesh Red Crescent Society')
            ->assertSee('Bangladesh Scouts')
            ->assertSee('Fire Service &amp; Civil Defence', false)
            ->assertSee('2026 MD. HABIB KHAN')
            ->assertSee('https://github.com/MdHabibKhan88', false);
    }

    public function test_bangla_language_is_used_when_selected(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->get('/lang/bn')->assertRedirect();
        $this->get('/')->assertOk()->assertSee('প্রজেক্ট')->assertSee('মোঃ হাবিব খান');
    }

    public function test_language_switch_rejects_unknown_locale(): void
    {
        $this->get('/lang/fr')->assertNotFound();
    }

    public function test_project_details_page_and_inactive_projects(): void
    {
        $this->seed(PortfolioSeeder::class);
        $project = Project::first();

        $this->get(route('projects.show', $project->slug))->assertOk()->assertSee($project->title_en);

        $project->update(['is_active' => false]);
        $this->get(route('projects.show', $project->slug))->assertNotFound();
    }

    public function test_custom_404_page(): void
    {
        $this->get('/this-page-does-not-exist')->assertNotFound()->assertSee('Page not found');
    }

    public function test_sitemap_and_robots(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->get('/sitemap.xml')->assertOk()->assertSee('<urlset', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin');
    }

    public function test_cv_download_redirects_when_missing_and_downloads_when_present(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->get('/cv/download')->assertRedirect();

        Storage::fake('public');
        Storage::disk('public')->put('cv/test.pdf', '%PDF-1.4 test');
        Profile::current()->update(['cv_file' => 'cv/test.pdf']);

        $this->get('/cv/download')->assertOk()->assertDownload('MD_Habib_Khan_CV.pdf');
    }

    public function test_no_public_admin_registration(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }
}
