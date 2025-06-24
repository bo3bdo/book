@extends('layouts.app')

@section('title', 'قراءة: ' . $book->title)
@section('description', 'اقرأ كتاب ' . $book->title . ' مباشرة في المتصفح')

@push('styles')
<style>
    /* منع الطباعة */
    @media print {
        body * {
            visibility: hidden !important;
        }
        body:before {
            content: "الطباعة غير مسموحة" !important;
            display: block !important;
            text-align: center !important;
            font-size: 2em !important;
            color: red !important;
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
        }
    }
    
    .pdf-reader-container {
        width: 100%;
        height: calc(100vh - 100px);
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        background: #f5f5f5;
    }
      .pdf-canvas-container {
        width: 100%;
        height: calc(100% - 60px);
        overflow: auto;
        background: #f5f5f5;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        direction: rtl; /* دعم الاتجاه العربي */
    }
      .pdf-page {
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background: white;
        border-radius: 4px;
        position: relative;
        direction: rtl; /* دعم اتجاه النص العربي */
    }    /* تحسين عرض النصوص العربية في Canvas */
    canvas {
        direction: rtl;
        unicode-bidi: bidi-override;
        font-family: 'AmiriLocal', 'ArabicFallback', 'Arial', 'Tahoma', 'Segoe UI', 'DejaVu Sans', sans-serif !important;
        -webkit-font-feature-settings: 'liga', 'kern', 'mark', 'mkmk';
        font-feature-settings: 'liga', 'kern', 'mark', 'mkmk';
        /* تحسين جودة النص */
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-display: swap;
    }
    
    /* فئة خاصة للنصوص العربية */
    .arabic-text, .pdf-page {
        font-family: 'AmiriLocal', 'ArabicFallback', 'Arial', 'Tahoma', sans-serif !important;
        direction: rtl !important;
        unicode-bidi: bidi-override !important;
        text-align: right !important;
    }
    
    .pdf-controls {
        height: 60px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
    }
    
    .pdf-controls .btn-group {
        display: flex;
        gap: 10px;
    }
    
    .pdf-info {
        color: #666;
        font-size: 14px;
    }
    
    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
    }
    
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
        margin-bottom: 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .reader-controls {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .book-info {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .fullscreen-btn {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: rgba(0,0,0,0.7);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 15px;
        cursor: pointer;
    }
    
    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        transition: width 0.3s ease;
        z-index: 1001;
    }
    
    /* منع التحديد والنسخ */
    * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }
    
    input, textarea, button {
        -webkit-user-select: auto;
        -moz-user-select: auto;
        -ms-user-select: auto;
        user-select: auto;
    }
    
    /* حماية Canvas */
    canvas {
        pointer-events: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    
    .protection-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        pointer-events: none;
        background: transparent;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Progress Bar -->
    <div class="reading-progress" id="reading-progress"></div>
    
    <!-- Fullscreen Button -->
    <button class="fullscreen-btn" onclick="toggleFullscreen()">
        <i class="fas fa-expand" id="fullscreen-icon"></i>
    </button>
    
    <div class="row">
        <!-- Sidebar with Book Info -->
        <div class="col-lg-3" id="sidebar">
            <!-- Book Information -->
            <div class="book-info">
                <div class="text-center mb-3">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" class="img-fluid rounded" style="max-height: 200px;" alt="{{ $book->title }}">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 200px;">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                    @endif
                </div>
                
                <h4 class="mb-2">{{ $book->title }}</h4>
                <p class="text-muted mb-2">
                    <i class="fas fa-user me-1"></i> {{ $book->author }}
                </p>
                <p class="text-muted mb-2">
                    <i class="fas fa-tag me-1"></i> 
                    <a href="{{ route('categories.show', $book->category) }}" class="text-decoration-none">
                        {{ $book->category->name }}
                    </a>
                </p>
                @if($book->pages)
                <p class="text-muted mb-2">
                    <i class="fas fa-file me-1"></i> {{ number_format($book->pages) }} صفحة
                </p>
                @endif
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>
                        العودة لتفاصيل الكتاب
                    </a>
                    
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSidebar()">
                        <i class="fas fa-eye-slash me-1"></i>
                        إخفاء الشريط الجانبي
                    </button>
                </div>
            </div>
            
            <!-- Reading Tips -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-1"></i>
                        نصائح للقراءة
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-1"></i> استخدم F11 للشاشة الكاملة</li>
                        <li><i class="fas fa-check text-success me-1"></i> تكبير/تصغير بأزرار التحكم</li>
                        <li><i class="fas fa-check text-success me-1"></i> التنقل بين الصفحات</li>
                        <li><i class="fas fa-check text-success me-1"></i> القراءة المحمية من التحميل</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- PDF Reader -->
        <div class="col-lg-9" id="viewer-container">
            <div class="reader-controls">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">{{ $book->title }}</h5>
                        <small class="text-muted">بواسطة {{ $book->author }}</small>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted small">قارئ PDF محمي</span>
                    </div>
                </div>
            </div>
            
            <div class="pdf-reader-container">
                <!-- أدوات التحكم في PDF -->
                <div class="pdf-controls">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="previousPage()" id="prevBtn" disabled>
                            <i class="fas fa-chevron-right"></i> السابق
                        </button>
                        <button class="btn btn-outline-secondary" onclick="nextPage()" id="nextBtn">
                            التالي <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                    
                    <div class="pdf-info">
                        <span id="pageInfo">صفحة 1 من 1</span>
                    </div>
                    
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="zoomOut()">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetZoom()">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="zoomIn()">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>
                
                <!-- حاوي الصفحات -->
                <div class="pdf-canvas-container" id="pdfContainer">
                    <div class="loading-container" id="loadingContainer">
                        <div class="loading-spinner"></div>
                        <p>جاري تحميل الكتاب...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- PDF.js Local -->
<script src="{{ asset('js/pdfjs/pdf.min.js') }}"></script>
<!-- ملف الخطوط العربية -->
<script src="{{ asset('js/pdfjs/arabic-fonts.js') }}"></script>
<script>
// إعدادات PDF.js للملفات المحلية
pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset("js/pdfjs/pdf.worker.min.js") }}';

// إعدادات دعم اللغة العربية
pdfjsLib.GlobalWorkerOptions.verbosity = pdfjsLib.VerbosityLevel.WARNINGS;

let pdfDoc = null;
let currentPage = 1;
let totalPages = 0;
let scale = 1.0;
let sidebarVisible = true;
let isFullscreen = false;

// منع الطباعة وحفظ الصفحة
document.addEventListener('keydown', function(e) {
    // منع Ctrl+P (طباعة)
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        e.stopPropagation();
        showMessage('الطباعة غير مسموحة', 'error');
        return false;
    }
    
    // منع Ctrl+S (حفظ)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        e.stopPropagation();
        showMessage('حفظ الملف غير مسموح', 'error');
        return false;
    }
    
    // منع F12 و أدوات المطور
    if (e.key === 'F12' || 
        (e.ctrlKey && e.shiftKey && e.key === 'I') ||
        (e.ctrlKey && e.shiftKey && e.key === 'C') ||
        (e.ctrlKey && e.shiftKey && e.key === 'J') ||
        (e.ctrlKey && e.key === 'u')) {
        e.preventDefault();
        e.stopPropagation();
        showMessage('هذه الأدوات غير متاحة', 'error');
        return false;
    }
    
    // منع تحديد الكل
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        e.stopPropagation();
        showMessage('تحديد النص غير مسموح', 'error');
        return false;
    }
    
    // التنقل بأسهم لوحة المفاتيح
    if (e.key === 'ArrowRight' || e.key === 'PageUp') {
        previousPage();
    } else if (e.key === 'ArrowLeft' || e.key === 'PageDown') {
        nextPage();
    }
}, true);

// منع النقر الأيمن تماماً
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    e.stopPropagation();
    showMessage('النقر بالزر الأيمن غير مسموح', 'warning');
    return false;
}, true);

// منع السحب والإفلات
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
    return false;
});

// منع التحديد
document.addEventListener('selectstart', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        return false;
    }
});

// منع النسخ
document.addEventListener('copy', function(e) {
    e.preventDefault();
    showMessage('نسخ النص غير مسموح', 'warning');
    return false;
});

// تحميل PDF مع دعم محلي للعربية والخطوط المحسّنة
async function loadPDF() {
    try {
        console.log('بدء تحميل PDF...');
        
        // التأكد من تحميل الخطوط العربية أولاً
        if (typeof enableArabicFontsForPDF === 'function') {
            await enableArabicFontsForPDF();
            console.log('تم تفعيل دعم الخطوط العربية');
            showMessage('تم تفعيل دعم الخطوط العربية', 'info');
        }
        
        // انتظار تحميل الخطوط
        await document.fonts.ready;
        console.log('الخطوط جاهزة');
        
        // رابط PDF للتشخيص
        const pdfUrl = '{{ route("books.serve", $book) }}';
        console.log('رابط PDF:', pdfUrl);
        
        // إعدادات تحميل محلية مع دعم محسّن للخطوط العربية
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            disableFontFace: false,
            disableRange: false,
            disableStream: false,
            fontExtraProperties: true
        });
        
        console.log('بدء تحميل مستند PDF...');
        pdfDoc = await loadingTask.promise;
        totalPages = pdfDoc.numPages;
        console.log('تم تحميل PDF بنجاح. عدد الصفحات:', totalPages);
        
        document.getElementById('loadingContainer').style.display = 'none';
        await renderPage(1);
        updateUI();
        
        showMessage('تم تحميل الكتاب بنجاح - النص العربي مدعوم محلياً', 'success');
    } catch (error) {
        console.error('خطأ مفصل في تحميل PDF:', error);
        document.getElementById('loadingContainer').innerHTML = `
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <p>حدث خطأ في تحميل الكتاب</p>
            <p class="small text-muted">الخطأ: ${error.message}</p>
            <p class="small text-muted">تأكد من وجود ملف PDF في المسار الصحيح</p>
            <button class="btn btn-primary" onclick="loadPDF()">إعادة المحاولة</button>
            <button class="btn btn-secondary" onclick="testPDFConnection()">اختبار الاتصال</button>
        `;
        showMessage('فشل في تحميل الكتاب: ' + error.message, 'error');
    }
}

// رسم الصفحة مع دعم محسن للعربية
async function renderPage(num) {
    try {
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale });
          // إنشاء canvas مع خصائص محسنة للنص العربي
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;
          // تحسين جودة النص العربي
        context.textAlign = 'right';
        context.direction = 'rtl';
        context.font = '16px AmiriLocal, ArabicFallback, Arial, Tahoma, sans-serif';
        
        // إضافة حماية للـ canvas
        canvas.style.pointerEvents = 'none';
        canvas.oncontextmenu = function() { return false; };
        canvas.onselectstart = function() { return false; };
        canvas.ondragstart = function() { return false; };
        
        // تطبيق فئة CSS للنص العربي
        canvas.classList.add('arabic-text');
        
        // إنشاء حاوي للصفحة
        const pageDiv = document.createElement('div');
        pageDiv.className = 'pdf-page';
        pageDiv.style.direction = 'rtl';
        pageDiv.appendChild(canvas);
        
        // إضافة طبقة حماية شفافة
        const protectionLayer = document.createElement('div');
        protectionLayer.className = 'protection-layer';
        protectionLayer.oncontextmenu = function() { return false; };
        protectionLayer.onselectstart = function() { return false; };
        protectionLayer.ondragstart = function() { return false; };
        pageDiv.appendChild(protectionLayer);
        
        // مسح الحاوي وإضافة الصفحة الجديدة
        const container = document.getElementById('pdfContainer');
        container.innerHTML = '';
        container.appendChild(pageDiv);
        
        // رسم الصفحة مع خيارات محسنة للنص
        const renderContext = {
            canvasContext: context,
            viewport: viewport,
            enableWebGL: false,
            renderInteractiveForms: true,
            textLayerMode: 0  // تعطيل طبقة النص لمنع النسخ
        };
        
        await page.render(renderContext).promise;
        currentPage = num;
        updateUI();
        
    } catch (error) {
        console.error('خطأ في رسم الصفحة:', error);
        showMessage('خطأ في عرض الصفحة', 'error');
    }
}

// تحديث واجهة المستخدم
function updateUI() {
    document.getElementById('pageInfo').textContent = `صفحة ${currentPage} من ${totalPages}`;
    document.getElementById('prevBtn').disabled = currentPage <= 1;
    document.getElementById('nextBtn').disabled = currentPage >= totalPages;
}

// التنقل بين الصفحات
function previousPage() {
    if (currentPage > 1) {
        renderPage(currentPage - 1);
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        renderPage(currentPage + 1);
    }
}

// التكبير والتصغير
function zoomIn() {
    scale *= 1.25;
    renderPage(currentPage);
}

function zoomOut() {
    scale *= 0.8;
    renderPage(currentPage);
}

function resetZoom() {
    scale = 1.0;
    renderPage(currentPage);
}

// الشريط الجانبي
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const viewerContainer = document.getElementById('viewer-container');
    
    if (sidebarVisible) {
        sidebar.style.display = 'none';
        viewerContainer.className = 'col-12';
        sidebarVisible = false;
    } else {
        sidebar.style.display = 'block';
        viewerContainer.className = 'col-lg-9';
        sidebarVisible = true;
    }
}

// الشاشة الكاملة
function toggleFullscreen() {
    const element = document.documentElement;
    const icon = document.getElementById('fullscreen-icon');
    
    if (!isFullscreen) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
        icon.className = 'fas fa-compress';
        isFullscreen = true;
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        icon.className = 'fas fa-expand';
        isFullscreen = false;
    }
}

// عرض الرسائل
function showMessage(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 
                      type === 'success' ? 'alert-success' :
                      'alert-info';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.top = '80px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 3000);
}

// تتبع تقدم القراءة
let readingStartTime = Date.now();
setInterval(function() {
    const timeElapsed = Date.now() - readingStartTime;
    const progressPercent = Math.min((timeElapsed / (1000 * 60 * 10)) * 100, 100);
    document.getElementById('reading-progress').style.width = progressPercent + '%';
}, 5000);

// إضافة watermark والتأكد من العمل بدون إنترنت
window.addEventListener('load', function() {
    // التحقق من حالة الاتصال
    const isOnline = navigator.onLine;
    if (!isOnline) {
        showMessage('النظام يعمل بدون إنترنت - الملفات محلية', 'info');
    }
    
    const watermark = document.createElement('div');
    watermark.innerHTML = '{{ config("app.name") }} - {{ $book->title }}';
    watermark.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 48px;
        color: rgba(0,0,0,0.05);
        pointer-events: none;
        z-index: 999;
        user-select: none;
        font-weight: bold;
        font-family: 'Arial', 'Tahoma', sans-serif;
    `;
    document.body.appendChild(watermark);
    
    // تحميل PDF
    loadPDF();
    
    // منع فتح أدوات المطور
    let devtools = {open: false, orientation: null};
    setInterval(function() {
        if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
            if (!devtools.open) {
                devtools.open = true;
                showMessage('تم رصد محاولة فتح أدوات المطور. يرجى إغلاقها.', 'warning');
            }
        } else {
            devtools.open = false;
        }
    }, 500);
    
    // مراقبة حالة الشبكة
    window.addEventListener('offline', function() {
        showMessage('تم قطع الاتصال - النظام يعمل محلياً', 'info');
    });
    
    window.addEventListener('online', function() {
        showMessage('تم استعادة الاتصال', 'success');
    });
});

// دالة لاختبار الاتصال بملف PDF
async function testPDFConnection() {
    try {
        const pdfUrl = '{{ route("books.serve", $book) }}';
        console.log('اختبار الاتصال بـ:', pdfUrl);
        
        const response = await fetch(pdfUrl);
        console.log('حالة الاستجابة:', response.status);
        console.log('نوع المحتوى:', response.headers.get('Content-Type'));
        
        if (response.ok) {
            const contentLength = response.headers.get('Content-Length');
            showMessage(`الملف متاح - الحجم: ${contentLength || 'غير محدد'} بايت`, 'success');
        } else {
            showMessage(`خطأ HTTP: ${response.status} - ${response.statusText}`, 'error');
        }
    } catch (error) {
        console.error('خطأ في اختبار الاتصال:', error);
        showMessage('فشل في الاتصال بملف PDF: ' + error.message, 'error');
    }
}

// منع الطباعة من المتصفح
window.print = function() {
    showMessage('الطباعة غير مسموحة', 'error');
    return false;
};
</script>
@endpush
@endsection
