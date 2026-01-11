<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (no organization)
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_org_admin' => false,
        ]);

        // Create a demo organization
        $demoOrg = Organization::create([
            'name' => 'Demo Organization',
            'slug' => 'demo-org',
            'description' => 'A demo organization for testing',
            'is_active' => true,
            'max_pages' => 10,
            'min_crawl_interval_hours' => 24,
        ]);

        // Create an organization user
        $orgUser = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'organization_id' => $demoOrg->id,
            'is_admin' => false,
            'is_org_admin' => true,
        ]);

        $orgUser->organizations()->sync([$demoOrg->id]);

        // Create some demo pages
        $pages = [
            [
                'name' => 'Google Homepage',
                'url' => 'https://www.google.com',
                'description' => 'Google search homepage',
            ],
            [
                'name' => 'GitHub',
                'url' => 'https://github.com',
                'description' => 'GitHub code hosting platform',
            ],
            [
                'name' => 'Laravel Documentation',
                'url' => 'https://laravel.com/docs',
                'description' => 'Official Laravel documentation',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::create([
                'organization_id' => $demoOrg->id,
                'name' => $pageData['name'],
                'url' => $pageData['url'],
                'description' => $pageData['description'],
                'is_active' => true,
                'crawl_interval_hours' => 24,
                'next_crawl_at' => now(),
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Admin Login:');
        $this->command->info('  Email: admin@example.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Demo User Login:');
        $this->command->info('  Email: demo@example.com');
        $this->command->info('  Password: password');
    }
}
