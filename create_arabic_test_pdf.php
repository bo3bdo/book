<?php
require_once 'vendor/autoload.php';

use Mpdf\Mpdf;

// إنشاء PDF باللغة العربية
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font_size' => 14,
    'default_font' => 'aealarabiya', // خط عربي
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 16,
    'margin_bottom' => 16,
    'margin_header' => 9,
    'margin_footer' => 9,
    'orientation' => 'P',
    'dir' => 'rtl' // اتجاه النص من اليمين لليسار
]);

$html = '
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "aealarabiya", "Arial", sans-serif;
            direction: rtl;
            text-align: right;
            line-height: 1.8;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .content {
            font-size: 16px;
            text-align: justify;
            margin: 20px 0;
        }
        .chapter {
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="title">كتاب تجريبي باللغة العربية</div>
    
    <div class="chapter">الفصل الأول: مقدمة</div>
    <div class="content">
        هذا نص تجريبي باللغة العربية لاختبار عرض ملفات PDF في المكتبة الإلكترونية. 
        يحتوي هذا النص على أحرف عربية متنوعة وعلامات التشكيل المختلفة.
        <br><br>
        نحن نختبر هنا قدرة المكتبة على عرض النصوص العربية بشكل صحيح، بما في ذلك:
        <br>
        • الأحرف الأساسية: أ ب ت ث ج ح خ د ذ ر ز س ش ص ض ط ظ ع غ ف ق ك ل م ن ه و ي
        <br>
        • علامات التشكيل: الفتحة َ والضمة ُ والكسرة ِ والسكون ْ والشدة ّ والتنوين
        <br>
        • الأرقام العربية: ١ ٢ ٣ ٤ ٥ ٦ ٧ ٨ ٩ ٠
        <br>
        • الأرقام الإنجليزية: 1 2 3 4 5 6 7 8 9 0
    </div>
    
    <div class="chapter">الفصل الثاني: نص تطبيقي</div>
    <div class="content">
        في عصر التكنولوجيا الرقمية، أصبحت المكتبات الإلكترونية جزءاً لا يتجزأ من حياتنا اليومية. 
        هذه المكتبات تُتيح لنا الوصول إلى آلاف الكتب والمراجع بسهولة ويسر، مما يُساهم في نشر المعرفة 
        وتسهيل عملية التعلم والبحث العلمي.
        <br><br>
        من أهم مميزات المكتبات الإلكترونية:
        <br>
        ١. سهولة الوصول من أي مكان وفي أي وقت
        <br>
        ٢. إمكانية البحث السريع في محتوى الكتب
        <br>
        ٣. توفير مساحة التخزين المادية
        <br>
        ٤. صداقة البيئة من خلال تقليل استخدام الورق
        <br>
        ٥. إمكانية مشاركة المحتوى بسهولة
    </div>
    
    <div class="chapter">الفصل الثالث: خاتمة</div>
    <div class="content">
        هذا المشروع يهدف إلى إنشاء مكتبة إلكترونية متكاملة تدعم عرض الكتب باللغة العربية بشكل 
        احترافي ومحمي من التحميل غير المرخص. نأمل أن تكون هذه المكتبة مفيدة للطلاب والباحثين 
        وجميع محبي القراءة والمعرفة.
        <br><br>
        شكراً لكم على استخدام مكتبتنا الإلكترونية، ونتمنى لكم تجربة قراءة ممتعة ومفيدة.
    </div>
</body>
</html>
';

$mpdf->WriteHTML($html);

// حفظ الملف
$outputPath = 'storage/app/public/books/pdfs/arabic_test.pdf';
$mpdf->Output($outputPath, 'F');

echo "تم إنشاء ملف PDF العربي التجريبي بنجاح في: " . $outputPath . "\n";
