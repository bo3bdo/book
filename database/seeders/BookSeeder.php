<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $books = [
            // الأدب العربي
            [
                'title' => 'الأسود يليق بك',
                'author' => 'أحلام مستغانمي',
                'description' => 'رواية عاطفية تحكي قصة حب معاصرة في إطار جزائري عربي، تتناول مواضيع الهوية والانتماء.',
                'category' => 'الأدب العربي',
                'language' => 'ar',
                'pages' => 352,
                'publication_year' => 2012,
                'is_featured' => true,
                'rating' => 4.5,
            ],
            [
                'title' => 'مئة عام من العزلة',
                'author' => 'غابرييل غارسيا ماركيز',
                'description' => 'رواية كلاسيكية مترجمة تحكي قصة عائلة بوينديا عبر أجيال في قرية ماكوندو الخيالية.',
                'category' => 'الأدب العربي',
                'language' => 'ar',
                'pages' => 448,
                'publication_year' => 1967,
                'is_featured' => false,
                'rating' => 4.7,
            ],

            // العلوم الإسلامية
            [
                'title' => 'صحيح البخاري',
                'author' => 'الإمام البخاري',
                'description' => 'أصح كتب الحديث النبوي الشريف، يحتوي على أحاديث مختارة بعناية مع الأسانيد الصحيحة.',
                'category' => 'العلوم الإسلامية',
                'language' => 'ar',
                'pages' => 2048,
                'publication_year' => 846,
                'is_featured' => true,
                'rating' => 5.0,
            ],
            [
                'title' => 'تفسير القرآن العظيم',
                'author' => 'ابن كثير',
                'description' => 'تفسير شامل للقرآن الكريم يجمع بين النقل والعقل مع شرح مفصل للآيات.',
                'category' => 'العلوم الإسلامية',
                'language' => 'ar',
                'pages' => 1856,
                'publication_year' => 1365,
                'is_featured' => false,
                'rating' => 4.9,
            ],

            // التكنولوجيا والحاسوب
            [
                'title' => 'تعلم Python من الصفر',
                'author' => 'محمد أحمد',
                'description' => 'دليل شامل لتعلم لغة البرمجة Python من البداية حتى الاحتراف مع أمثلة عملية.',
                'category' => 'التكنولوجيا والحاسوب',
                'language' => 'ar',
                'pages' => 456,
                'publication_year' => 2023,
                'is_featured' => true,
                'rating' => 4.3,
            ],
            [
                'title' => 'الذكاء الاصطناعي والتعلم الآلي',
                'author' => 'د. علي حسن',
                'description' => 'مقدمة شاملة في الذكاء الاصطناعي والتعلم الآلي مع التطبيقات العملية.',
                'category' => 'التكنولوجيا والحاسوب',
                'language' => 'ar',
                'pages' => 624,
                'publication_year' => 2022,
                'is_featured' => false,
                'rating' => 4.4,
            ],

            // التاريخ والتراث
            [
                'title' => 'تاريخ الطبري',
                'author' => 'محمد بن جرير الطبري',
                'description' => 'موسوعة تاريخية شاملة تغطي تاريخ العالم من بداية الخليقة حتى عصر المؤلف.',
                'category' => 'التاريخ والتراث',
                'language' => 'ar',
                'pages' => 3200,
                'publication_year' => 915,
                'is_featured' => false,
                'rating' => 4.6,
            ],
            [
                'title' => 'حضارة الإسلام في دار السلام',
                'author' => 'د. عبد الحليم عويس',
                'description' => 'دراسة معاصرة لحضارة الإسلام وإنجازاتها في مختلف المجالات.',
                'category' => 'التاريخ والتراث',
                'language' => 'ar',
                'pages' => 512,
                'publication_year' => 1995,
                'is_featured' => true,
                'rating' => 4.4,
            ],

            // علم النفس والاجتماع
            [
                'title' => 'علم النفس التربوي',
                'author' => 'د. نايفة قطامي',
                'description' => 'دراسة شاملة في علم النفس التربوي وتطبيقاته في التعليم والتعلم.',
                'category' => 'علم النفس والاجتماع',
                'language' => 'ar',
                'pages' => 384,
                'publication_year' => 2018,
                'is_featured' => false,
                'rating' => 4.2,
            ],
            [
                'title' => 'السلوك الإنساني في المنظمات',
                'author' => 'د. أحمد ماهر',
                'description' => 'دراسة سلوك الأفراد والجماعات في بيئة العمل وكيفية إدارتها بفعالية.',
                'category' => 'علم النفس والاجتماع',
                'language' => 'ar',
                'pages' => 468,
                'publication_year' => 2020,
                'is_featured' => true,
                'rating' => 4.1,
            ],
        ];

        foreach ($books as $bookData) {
            $category = $categories->where('name', $bookData['category'])->first();

            if ($category) {
                Book::create([
                    'title' => $bookData['title'],
                    'slug' => Str::slug($bookData['title']),
                    'description' => $bookData['description'],
                    'author' => $bookData['author'],
                    'category_id' => $category->id,
                    'language' => $bookData['language'],
                    'pages' => $bookData['pages'],
                    'publication_year' => $bookData['publication_year'],
                    'is_featured' => $bookData['is_featured'],
                    'is_active' => true,
                    'rating' => $bookData['rating'],
                    'pdf_file' => 'books/pdfs/sample.pdf', // ملف تجريبي
                    'download_count' => rand(10, 1000),
                    'view_count' => rand(50, 5000),
                ]);
            }
        }
    }
}
