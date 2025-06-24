<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار PDF.js محلياً</title>
    <style>
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            direction: rtl;
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .success {
            color: #28a745;
            font-size: 18px;
            margin: 20px 0;
        }
        .error {
            color: #dc3545;
            font-size: 18px;
            margin: 20px 0;
        }
        .info {
            color: #007bff;
            font-size: 16px;
            margin: 15px 0;
        }
        .test-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .test-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>اختبار مكتبة PDF.js المحلية</h1>
        
        <div id="results">
            <p>جاري اختبار النظام...</p>
        </div>
          <button class="test-btn" onclick="testPDFJs()">اختبار PDF.js</button>
        <button class="test-btn" onclick="testArabicFonts()">اختبار الخطوط العربية</button>
        <button class="test-btn" onclick="testNetwork()">اختبار الشبكة</button>
        <button class="test-btn" onclick="window.location.href='{{ route('books.index') }}'">العودة للمكتبة</button>
    </div>

    <script src="{{ asset('js/pdfjs/pdf.min.js') }}"></script>
    <script src="{{ asset('js/pdfjs/arabic-fonts.js') }}"></script>
    <script>
        const results = document.getElementById('results');
        
        function addResult(message, type = 'info') {
            const div = document.createElement('div');
            div.className = type;
            div.textContent = message;
            results.appendChild(div);
        }
        
        async function testArabicFonts() {
            results.innerHTML = '<p>جاري اختبار الخطوط العربية...</p>';
            
            try {                // اختبار وجود الخطوط العربية
                if (typeof ArabicFonts !== 'undefined') {
                    addResult('✓ ملف الخطوط العربية الجديد محمّل', 'success');
                    
                    // اختبار تسجيل الخطوط
                    if (typeof enableArabicFontsForPDF === 'function') {
                        await enableArabicFontsForPDF();
                        addResult('✓ تم تفعيل دعم الخطوط العربية المحسّنة', 'success');
                    }
                    
                    // اختبار عرض النص العربي بالخط الجديد
                    const testDiv = document.createElement('div');
                    testDiv.style.fontFamily = 'AmiriLocal, ArabicFallback, Arial, Tahoma';
                    testDiv.style.direction = 'rtl';
                    testDiv.style.fontSize = '18px';
                    testDiv.textContent = 'هذا نص تجريبي بالخط العربي المحسّن - ١٢٣٤٥٦٧٨٩٠';
                    testDiv.style.padding = '15px';
                    testDiv.style.background = '#e8f5e8';
                    testDiv.style.border = '2px solid #4caf50';
                    testDiv.style.borderRadius = '5px';
                    testDiv.style.margin = '10px 0';
                    testDiv.className = 'arabic-text';
                    results.appendChild(testDiv);
                    
                    addResult('✓ تم عرض نص تجريبي بالخط العربي المحسّن أعلاه', 'success');
                    
                    // اختبار وجود ملف الخط المحلي
                    try {
                        const response = await fetch('./js/pdfjs/amiri-arabic.woff2');
                        if (response.ok) {
                            addResult('✓ ملف الخط العربي المحلي متوفر', 'success');
                        } else {
                            addResult('⚠ ملف الخط العربي المحلي غير متوفر، سيتم استخدام الخط الاحتياطي', 'info');
                        }
                    } catch (error) {
                        addResult('⚠ خطأ في الوصول لملف الخط المحلي: ' + error.message, 'info');
                    }
                    
                    // اختبار إعدادات PDF.js للخطوط
                    if (typeof pdfjsLib !== 'undefined') {
                        if (!pdfjsLib.GlobalWorkerOptions.disableFontFace) {
                            addResult('✓ PDF.js مُعد لاستخدام الخطوط المخصصة', 'success');
                        } else {
                            addResult('⚠ PDF.js قد لا يستخدم الخطوط المخصصة', 'error');
                        }
                    }
                    
                } else {
                    addResult('✗ ملف الخطوط العربية غير محمّل', 'error');
                }
            } catch (error) {
                addResult('✗ خطأ في اختبار الخطوط العربية: ' + error.message, 'error');
            }
        }
        
        function testPDFJs() {
            results.innerHTML = '<p>جاري اختبار PDF.js...</p>';
            
            try {
                // اختبار وجود PDF.js
                if (typeof pdfjsLib !== 'undefined') {
                    addResult('✓ مكتبة PDF.js محملة بنجاح', 'success');
                    
                    // اختبار Worker
                    if (pdfjsLib.GlobalWorkerOptions.workerSrc) {
                        addResult('✓ PDF Worker مُعرّف: ' + pdfjsLib.GlobalWorkerOptions.workerSrc, 'success');
                    } else {
                        addResult('✗ PDF Worker غير مُعرّف', 'error');
                    }
                    
                    // اختبار إصدار المكتبة
                    if (pdfjsLib.version) {
                        addResult('✓ إصدار PDF.js: ' + pdfjsLib.version, 'info');
                    }
                    
                } else {
                    addResult('✗ مكتبة PDF.js غير محملة', 'error');
                }
            } catch (error) {
                addResult('✗ خطأ في اختبار PDF.js: ' + error.message, 'error');
            }
        }
        
        function testNetwork() {
            results.innerHTML = '<p>جاري اختبار الشبكة...</p>';
            
            if (navigator.onLine) {
                addResult('✓ الاتصال بالإنترنت متوفر', 'success');
            } else {
                addResult('✓ النظام يعمل بدون إنترنت (محلياً)', 'success');
            }
            
            // اختبار تحميل الملفات المحلية
            fetch('{{ asset("js/pdfjs/pdf.min.js") }}')
                .then(response => {
                    if (response.ok) {
                        addResult('✓ ملف PDF.js الرئيسي متوفر محلياً', 'success');
                    } else {
                        addResult('✗ ملف PDF.js الرئيسي غير متوفر', 'error');
                    }
                })
                .catch(error => {
                    addResult('✗ خطأ في الوصول لملف PDF.js: ' + error.message, 'error');
                });
                
            fetch('{{ asset("js/pdfjs/pdf.worker.min.js") }}')
                .then(response => {
                    if (response.ok) {
                        addResult('✓ ملف PDF Worker متوفر محلياً', 'success');
                    } else {
                        addResult('✗ ملف PDF Worker غير متوفر', 'error');
                    }
                })
                .catch(error => {
                    addResult('✗ خطأ في الوصول لملف PDF Worker: ' + error.message, 'error');
                });
        }
        
        // تشغيل الاختبارات تلقائياً
        window.addEventListener('load', function() {
            setTimeout(testPDFJs, 1000);
            setTimeout(testNetwork, 2000);
        });
    </script>
</body>
</html>
