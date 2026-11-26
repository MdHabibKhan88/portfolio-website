<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GalleryItem;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SkillCategory;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'profile' => Profile::current(),
            'categories' => SkillCategory::active()->ordered()
                ->with(['skills' => fn ($q) => $q->active()->ordered()])->get(),
            'projects' => Project::active()->ordered()->get(),
            'education' => Education::active()->ordered()->get(),
            'experiences' => Experience::active()->ordered()->get(),
            'volunteers' => Volunteer::active()->ordered()->get(),
            'certificates' => Certificate::active()->ordered()->get(),
            'achievements' => Achievement::active()->ordered()->get(),
            'gallery' => GalleryItem::active()->ordered()->get(),
        ]);
    }

    public function project(string $slug)
    {
        $project = Project::active()->where('slug', $slug)->firstOrFail();

        return view('projects.show', compact('project'));
    }

    public function cv()
    {
        $path = Profile::current()->cv_file;

        if (blank($path) || ! Storage::disk('public')->exists($path)) {
            return redirect()->to(route('home').'#home')->with('cv_missing', true);
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($path, 'MD_Habib_Khan_CV.'.$ext);
    }

    public function sitemap()
    {
        $urls = [['loc' => url('/'), 'mod' => null]];
        foreach (Project::active()->ordered()->get() as $p) {
            $urls[] = ['loc' => route('projects.show', $p->slug), 'mod' => $p->updated_at?->toAtomString()];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>'.e($u['loc']).'</loc>'.($u['mod'] ? '<lastmod>'.$u['mod'].'</lastmod>' : '').'</url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $txt = "User-agent: *\nDisallow: /admin\nSitemap: ".url('/sitemap.xml')."\n";

        return response($txt, 200, ['Content-Type' => 'text/plain']);
    }
}
