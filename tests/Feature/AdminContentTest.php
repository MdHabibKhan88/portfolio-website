<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
        Storage::fake('public');
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'correct-horse-9', 'is_admin' => true,
        ]));
    }

    public function test_all_admin_pages_load(): void
    {
        $pages = [
            '/admin', '/admin/dashboard', '/admin/page/profile', '/admin/page/about', '/admin/page/cv',
            '/admin/settings', '/admin/translations', '/admin/account', '/admin/messages',
        ];
        foreach (['projects', 'skills', 'skill-categories', 'education', 'experience', 'volunteers',
                  'certificates', 'achievements', 'gallery', 'social-links'] as $r) {
            $pages[] = "/admin/$r";
            $pages[] = "/admin/$r/create";
        }

        foreach ($pages as $page) {
            $response = $this->get($page);
            if ($page === '/admin') {
                $response->assertRedirect('/admin/dashboard');
            } else {
                $response->assertOk();
            }
        }
    }

    public function test_project_crud_and_public_visibility(): void
    {
        $this->post('/admin/projects', [
            'title_en' => 'My New Project', 'title_bn' => 'আমার নতুন প্রজেক্ট', 'github_url' => 'https://github.com/x/y',
            'technologies' => 'PHP, Laravel', 'is_active' => 1,
        ])->assertRedirect('/admin/projects');

        $project = Project::where('title_en', 'My New Project')->firstOrFail();
        $this->assertSame('my-new-project', $project->slug);
        $this->get('/')->assertSee('My New Project');

        $this->put("/admin/projects/{$project->id}", ['title_en' => 'Renamed Project', 'is_active' => 1])->assertRedirect();
        $this->get('/')->assertSee('Renamed Project')->assertDontSee('My New Project');

        $this->post("/admin/projects/{$project->id}/toggle")->assertRedirect();
        $this->get('/')->assertDontSee('Renamed Project');

        $this->delete("/admin/projects/{$project->id}")->assertRedirect('/admin/projects');
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_project_validation(): void
    {
        $this->post('/admin/projects', ['title_en' => '', 'github_url' => 'not-a-url'])
            ->assertSessionHasErrors(['title_en', 'github_url']);

        $this->post('/admin/projects', ['title_en' => 'Pneumonia Detection System'])->assertSessionHasErrors('title_en');
    }

    public function test_project_image_upload_replace_and_validation(): void
    {
        $this->post('/admin/projects', ['title_en' => 'With Image', 'image' => UploadedFile::fake()->image('a.jpg'), 'is_active' => 1])->assertRedirect();
        $project = Project::where('title_en', 'With Image')->firstOrFail();
        Storage::disk('public')->assertExists($project->image);
        $old = $project->image;

        $this->put("/admin/projects/{$project->id}", ['title_en' => 'With Image', 'image' => UploadedFile::fake()->image('b.png'), 'is_active' => 1])->assertRedirect();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($project->fresh()->image);

        $this->post('/admin/projects', ['title_en' => 'Bad Upload', 'image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])
            ->assertSessionHasErrors('image');
        $this->post('/admin/projects', ['title_en' => 'Too Big', 'image' => UploadedFile::fake()->image('big.jpg')->size(5000)])
            ->assertSessionHasErrors('image');
    }

    public function test_certificate_pdf_upload_appears_on_public_site(): void
    {
        $this->post('/admin/certificates', [
            'title_en' => 'Python Basics', 'issuer_en' => 'Some Academy',
            'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'), 'is_active' => 1,
        ])->assertRedirect('/admin/certificates');

        $this->get('/')->assertSee('Python Basics')->assertSee('Some Academy');
        $this->post('/admin/certificates', ['title_en' => 'Bad', 'file' => UploadedFile::fake()->create('x.exe', 10, 'application/x-msdownload')])
            ->assertSessionHasErrors('file');
    }

    public function test_skills_are_unique_per_category_and_editable(): void
    {
        $cat = SkillCategory::first();
        $other = SkillCategory::skip(1)->first();

        $this->post('/admin/skills', ['skill_category_id' => $cat->id, 'name' => 'Rust', 'level' => 40, 'is_active' => 1])->assertRedirect();
        $this->post('/admin/skills', ['skill_category_id' => $cat->id, 'name' => 'Rust'])->assertSessionHasErrors('name');
        $this->post('/admin/skills', ['skill_category_id' => $other->id, 'name' => 'Rust', 'is_active' => 1])->assertSessionHasNoErrors();
        $this->post('/admin/skills', ['skill_category_id' => 9999, 'name' => 'Ghost'])->assertSessionHasErrors('skill_category_id');
        $this->post('/admin/skills', ['skill_category_id' => $cat->id, 'name' => 'TooHigh', 'level' => 150])->assertSessionHasErrors('level');

        $skill = Skill::where('name', 'Rust')->where('skill_category_id', $cat->id)->firstOrFail();
        $this->put("/admin/skills/{$skill->id}", ['skill_category_id' => $cat->id, 'name' => 'Rust Lang', 'level' => 55, 'is_active' => 1])->assertRedirect();
        $this->get('/')->assertSee('Rust Lang');
    }

    public function test_reordering_changes_public_order(): void
    {
        $this->post('/admin/projects', ['title_en' => 'Zeta Project', 'is_active' => 1]);
        $zeta = Project::where('title_en', 'Zeta Project')->firstOrFail();

        $this->post("/admin/projects/{$zeta->id}/move/up")->assertRedirect();
        $this->post("/admin/projects/{$zeta->id}/move/up")->assertRedirect();
        $this->post("/admin/projects/{$zeta->id}/move/up")->assertRedirect();

        $this->assertSame($zeta->id, Project::ordered()->first()->id);
    }

    public function test_profile_about_and_cv_management(): void
    {
        $this->put('/admin/page/profile', ['name_en' => 'Habib Test', 'name_bn' => 'হাবিব', 'email' => 'new@example.com'])->assertRedirect();
        $this->get('/')->assertSee('Habib Test');

        $this->put('/admin/page/profile', ['name_en' => 'Habib Test', 'photo' => UploadedFile::fake()->image('me.jpg')])->assertRedirect();
        $this->assertNotNull(\App\Models\Profile::current()->photo);

        $this->put('/admin/page/about', ['about_en' => 'Fresh about text'])->assertRedirect();
        $this->get('/')->assertSee('Fresh about text');

        $this->put('/admin/page/cv', ['cv_file' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf')])->assertRedirect();
        Storage::disk('public')->assertExists(\App\Models\Profile::current()->cv_file);
        $this->put('/admin/page/cv', ['cv_file' => UploadedFile::fake()->create('cv.exe', 10, 'application/x-msdownload')])->assertSessionHasErrors('cv_file');
    }

    public function test_social_links_update_public_links(): void
    {
        $this->post('/admin/social-links', ['platform' => 'Website', 'url' => 'https://example.org/me', 'icon' => 'fa-solid fa-globe', 'is_active' => 1])->assertRedirect();
        $this->get('/')->assertSee('https://example.org/me', false);
    }

    public function test_contact_messages_can_be_viewed_and_deleted(): void
    {
        $m = ContactMessage::create(['name' => 'Karim', 'email' => 'k@example.com', 'subject' => 'Hi there', 'message' => 'Message body here']);

        $this->get('/admin/messages')->assertOk()->assertSee('Hi there');
        $this->get("/admin/messages/{$m->id}")->assertOk()->assertSee('Message body here');
        $this->assertTrue($m->fresh()->is_read);

        $this->delete("/admin/messages/{$m->id}")->assertRedirect('/admin/messages');
        $this->assertDatabaseMissing('contact_messages', ['id' => $m->id]);
    }

    public function test_settings_and_language_content(): void
    {
        $this->put('/admin/settings', ['site_title_en' => 'My Custom Title', 'footer_tagline_en' => 'Custom tagline'])->assertRedirect();
        $this->get('/')->assertSee('My Custom Title')->assertSee('Custom tagline');

        $this->put('/admin/translations', ['en' => ['nav_home' => 'Start'], 'bn' => ['nav_home' => 'শুরু']])->assertRedirect();
        $this->get('/')->assertSee('Start');
        $this->withSession(['locale' => 'bn'])->get('/')->assertSee('শুরু');
    }

    public function test_account_password_change(): void
    {
        $this->put('/admin/account/password', ['current_password' => 'wrong', 'password' => 'new-strong-pass-1', 'password_confirmation' => 'new-strong-pass-1'])
            ->assertSessionHasErrors('current_password');
        $this->put('/admin/account/password', ['current_password' => 'correct-horse-9', 'password' => 'weak', 'password_confirmation' => 'weak'])
            ->assertSessionHasErrors('password');
        $this->put('/admin/account/password', ['current_password' => 'correct-horse-9', 'password' => 'new-strong-pass-1', 'password_confirmation' => 'new-strong-pass-1'])
            ->assertSessionHasNoErrors();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-strong-pass-1', User::first()->password));
    }

    public function test_unknown_admin_resource_is_404(): void
    {
        $this->get('/admin/not-a-resource')->assertNotFound();
    }
}
