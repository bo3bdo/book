<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الأدب العربي',
                'description' => 'كتب الأدب والشعر والقصص العربية الكلاسيكية والمعاصرة',
            ],
            [
                'name' => 'العلوم الإسلامية',
                'description' => 'كتب التفسير والحديث والفقه والعقيدة الإسلامية',
            ],
            [
                'name' => 'التاريخ والتراث',
                'description' => 'كتب التاريخ العربي والإسلامي والحضارات القديمة',
            ],
            [
                'name' => 'الفلسفة والفكر',
                'description' => 'كتب الفلسفة والمنطق والفكر الإسلامي والعربي',
            ],
            [
                'name' => 'العلوم الطبيعية',
                'description' => 'كتب الفيزياء والكيمياء والأحياء والرياضيات',
            ],
            [
                'name' => 'الطب والصحة',
                'description' => 'كتب الطب والصحة العامة والطب البديل',
            ],
            [
                'name' => 'التكنولوجيا والحاسوب',
                'description' => 'كتب البرمجة وتقنية المعلومات والذكاء الاصطناعي',
            ],
            [
                'name' => 'الاقتصاد والمال',
                'description' => 'كتب الاقتصاد والتمويل والاستثمار والأعمال',
            ],
            [
                'name' => 'علم النفس والاجتماع',
                'description' => 'كتب علم النفس والتربية والعلوم الاجتماعية',
            ],
            [
                'name' => 'السيرة والتراجم',
                'description' => 'كتب السيرة النبوية وتراجم العلماء والشخصيات التاريخية',
            ],
            [
                'name' => 'الجغرافيا والسفر',
                'description' => 'كتب الجغرافيا وأدب الرحلات ووصف البلدان',
            ],
            [
                'name' => 'الفنون والآداب',
                'description' => 'كتب الفنون الجميلة والموسيقى والآداب المختلفة',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => true,
            ]);
        }
    }
}
