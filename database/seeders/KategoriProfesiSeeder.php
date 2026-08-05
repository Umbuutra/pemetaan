<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriProfesiSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            [
                'nama_kategori' => 'Software Engineering & Development',
                'slug' => 'software-engineering',
                'deskripsi' => 'Web Developer, Mobile Developer, Backend, Frontend, Fullstack Engineer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Data Science & Artificial Intelligence',
                'slug' => 'data-science-ai',
                'deskripsi' => 'Data Analyst, Data Engineer, Machine Learning Specialist, AI Engineer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'UI/UX & Product Design',
                'slug' => 'ui-ux-design',
                'deskripsi' => 'UI/UX Designer, Product Designer, Interaction Designer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Cyber Security & Network Engineering',
                'slug' => 'cyber-security-network',
                'deskripsi' => 'Security Engineer, Network Administrator, Cloud Engineer, DevOps Specialist',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Finance, Banking & Accounting',
                'slug' => 'finance-accounting',
                'deskripsi' => 'Accountant, Financial Analyst, Auditor, Tax Consultant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Digital Marketing & Growth',
                'slug' => 'digital-marketing',
                'deskripsi' => 'SEO Specialist, Content Creator, Social Media Specialist, Growth Marketer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Wirausaha & StartUp Founder',
                'slug' => 'wirausaha-founder',
                'deskripsi' => 'Business Owner, Startup Founder, Entrepreneur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Pendidikan & Riset',
                'slug' => 'pendidikan-riset',
                'deskripsi' => 'Dosen, Guru, Researcher, Academic Consultant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kategori_profesi')->insert($kategori);
    }
}
