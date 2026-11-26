<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

/**
 * Initial content. Uses firstOrCreate so re-running never overwrites edits made in the dashboard.
 * Only facts supplied by the owner are seeded; everything else is left empty for the admin to fill in.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        if (Profile::count() === 0) {
            Profile::create([
                'name_en' => 'MD. HABIB KHAN',
                'name_bn' => 'মোঃ হাবিব খান',
                'headline_en' => 'CSE Student | Full-Stack Developer | Aspiring Data Analyst | AI/ML Enthusiast',
                'headline_bn' => 'সিএসই শিক্ষার্থী | ফুল-স্ট্যাক ডেভেলপার | ডেটা অ্যানালিস্ট হতে আগ্রহী | এআই/এমএল অনুরাগী',
                'intro_en' => 'I am a Computer Science and Engineering student at Khwaja Yunus Ali University (KYAU). I enjoy building web applications and exploring data analysis and machine learning.',
                'intro_bn' => 'আমি খাজা ইউনুস আলী বিশ্ববিদ্যালয় (KYAU)-এর কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং বিভাগের শিক্ষার্থী। ওয়েব অ্যাপ্লিকেশন তৈরি করা এবং ডেটা অ্যানালাইসিস ও মেশিন লার্নিং নিয়ে কাজ করতে আমার ভালো লাগে।',
                'university_en' => 'Khwaja Yunus Ali University (KYAU)',
                'university_bn' => 'খাজা ইউনুস আলী বিশ্ববিদ্যালয় (KYAU)',
                'department_en' => 'Computer Science and Engineering (CSE)',
                'department_bn' => 'কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং (সিএসই)',
                'email' => 'mdhabibk760@gmail.com',
                'about_en' => 'I am a CSE student interested in full-stack web development, data analysis and AI/ML. (Edit this text from Admin > About Me.)',
                'about_bn' => 'আমি সিএসই-এর একজন শিক্ষার্থী। ফুল-স্ট্যাক ওয়েব ডেভেলপমেন্ট, ডেটা অ্যানালাইসিস এবং এআই/এমএল আমার আগ্রহের বিষয়। (অ্যাডমিন > আমার সম্পর্কে থেকে এই লেখা বদলাতে পারবেন।)',
                'academic_en' => 'Studying Computer Science and Engineering at Khwaja Yunus Ali University (KYAU).',
                'academic_bn' => 'খাজা ইউনুস আলী বিশ্ববিদ্যালয় (KYAU)-এ কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং নিয়ে পড়ছি।',
                'interests_en' => 'Full-stack web development, data analysis, data visualization, machine learning.',
                'interests_bn' => 'ফুল-স্ট্যাক ওয়েব ডেভেলপমেন্ট, ডেটা অ্যানালাইসিস, ডেটা ভিজ্যুয়ালাইজেশন, মেশিন লার্নিং।',
                'objectives_en' => 'To grow as a full-stack developer and data analyst, and to apply machine learning to practical problems.',
                'objectives_bn' => 'একজন ফুল-স্ট্যাক ডেভেলপার ও ডেটা অ্যানালিস্ট হিসেবে নিজেকে গড়ে তোলা এবং বাস্তব সমস্যায় মেশিন লার্নিং প্রয়োগ করা।',
            ]);
        }

        // Social links
        foreach ([
            ['GitHub', 'https://github.com/MdHabibKhan88', 'fa-brands fa-github'],
            ['LinkedIn', 'https://www.linkedin.com/in/habib-khan-6964a8427', 'fa-brands fa-linkedin-in'],
            ['Facebook', 'https://www.facebook.com/s.k.habib.khan.3914', 'fa-brands fa-facebook-f'],
            ['Instagram', 'https://www.instagram.com/md.habib77.88', 'fa-brands fa-instagram'],
            ['ResearchGate', 'https://www.researchgate.net/profile/Md-Khan-462', 'fa-brands fa-researchgate'],
        ] as $i => [$platform, $url, $icon]) {
            SocialLink::firstOrCreate(['platform' => $platform], ['url' => $url, 'icon' => $icon, 'sort_order' => $i + 1]);
        }

        // Projects (descriptions are intentionally short; edit them in the dashboard)
        foreach ([
            ['Pneumonia Detection System', 'নিউমোনিয়া ডিটেকশন সিস্টেম', 'https://github.com/MdHabibKhan88/pneumonia-detection', 'Python, Machine Learning', 'Machine Learning',
                'A pneumonia detection project built with machine learning.', 'মেশিন লার্নিং দিয়ে তৈরি একটি নিউমোনিয়া শনাক্তকরণ প্রজেক্ট।'],
            ['KYAF Food Donation Management System', 'KYAF ফুড ডোনেশন ম্যানেজমেন্ট সিস্টেম', 'https://github.com/MdHabibKhan88/KYAF-Food-Donation-Management-System', 'PHP, Laravel, MySQL', 'Web Application',
                'A web application for managing food donations.', 'খাদ্য অনুদান পরিচালনার জন্য একটি ওয়েব অ্যাপ্লিকেশন।'],
            ['Student Registration System', 'স্টুডেন্ট রেজিস্ট্রেশন সিস্টেম', 'https://github.com/MdHabibKhan88/Student_Registration_Project', null, 'Web Application',
                'A student registration project.', 'একটি স্টুডেন্ট রেজিস্ট্রেশন প্রজেক্ট।'],
        ] as $i => [$en, $bn, $github, $tech, $cat, $descEn, $descBn]) {
            Project::firstOrCreate(['title_en' => $en], [
                'title_bn' => $bn, 'github_url' => $github, 'technologies' => $tech, 'category' => $cat,
                'short_description_en' => $descEn, 'short_description_bn' => $descBn,
                'description_en' => $descEn, 'description_bn' => $descBn,
                'is_featured' => true, 'sort_order' => $i + 1,
            ]);
        }

        // Skill categories + skills. Levels are PLACEHOLDERS - set your real levels in the dashboard.
        $skills = [
            ['Programming Languages', 'প্রোগ্রামিং ল্যাঙ্গুয়েজ', 'fa-solid fa-code', ['Python' => 70, 'PHP' => 70, 'JavaScript' => 60]],
            ['Web Development', 'ওয়েব ডেভেলপমেন্ট', 'fa-solid fa-globe', ['Laravel' => 65, 'HTML' => 80, 'CSS' => 75]],
            ['Database', 'ডেটাবেজ', 'fa-solid fa-database', ['MySQL' => 70]],
            ['Machine Learning', 'মেশিন লার্নিং', 'fa-solid fa-brain', ['Machine Learning' => 60]],
            ['Data Analysis', 'ডেটা অ্যানালাইসিস', 'fa-solid fa-chart-column', ['Data Analysis' => 65, 'Microsoft Excel' => 70, 'Data Visualization' => 65]],
            ['Tools & Technologies', 'টুলস ও টেকনোলজি', 'fa-solid fa-screwdriver-wrench', ['Problem Solving' => 75]],
        ];
        foreach ($skills as $ci => [$en, $bn, $icon, $items]) {
            $cat = SkillCategory::firstOrCreate(['name_en' => $en], ['name_bn' => $bn, 'icon' => $icon, 'sort_order' => $ci + 1]);
            $n = 0;
            foreach ($items as $name => $level) {
                Skill::firstOrCreate(['skill_category_id' => $cat->id, 'name' => $name], ['level' => $level, 'sort_order' => ++$n]);
            }
        }

        // Education: only what was supplied (university + department). No dates invented.
        Education::firstOrCreate(['institution_en' => 'Khwaja Yunus Ali University (KYAU)'], [
            'institution_bn' => 'খাজা ইউনুস আলী বিশ্ববিদ্যালয় (KYAU)',
            'degree_en' => 'Computer Science and Engineering (CSE)',
            'degree_bn' => 'কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং (সিএসই)',
            'is_current' => true, 'sort_order' => 1,
        ]);

        // Volunteer organizations: names only. Add roles, dates and descriptions in the dashboard.
        foreach ([
            ['Bangladesh Red Crescent Society', 'বাংলাদেশ রেড ক্রিসেন্ট সোসাইটি'],
            ['Bangladesh Scouts', 'বাংলাদেশ স্কাউটস'],
            ['Fire Service & Civil Defence', 'ফায়ার সার্ভিস ও সিভিল ডিফেন্স'],
        ] as $i => [$en, $bn]) {
            Volunteer::firstOrCreate(['organization_en' => $en], ['organization_bn' => $bn, 'sort_order' => $i + 1]);
        }

        // Website settings
        foreach ([
            'site_title' => ['MD. HABIB KHAN | Portfolio', 'মোঃ হাবিব খান | পোর্টফোলিও'],
            'meta_description' => [
                'Portfolio of MD. HABIB KHAN, CSE student, full-stack developer, aspiring data analyst and AI/ML enthusiast.',
                'মোঃ হাবিব খান-এর পোর্টফোলিও — সিএসই শিক্ষার্থী, ফুল-স্ট্যাক ডেভেলপার, ডেটা অ্যানালিস্ট হতে আগ্রহী ও এআই/এমএল অনুরাগী।',
            ],
            'footer_tagline' => ['Learning, building and sharing.', 'শিখছি, বানাচ্ছি, ভাগ করছি।'],
        ] as $key => [$en, $bn]) {
            Setting::firstOrCreate(['key' => $key], ['value_en' => $en, 'value_bn' => $bn]);
        }
    }
}
