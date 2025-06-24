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
    
    .pdf-viewer-container {
        width: 100%;
        height: calc(100vh - 100px);
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        background: #f5f5f5;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 100%;
        border: none;
        pointer-events: auto;
    }
    
    .alternative-viewer {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: white;
    }
    
    /* منع التحديد والنسخ */
    .pdf-viewer-container {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
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
    
    /* منع السحب والإفلات */
    * {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
    }
    
    .viewer-option {
        margin: 10px;
        padding: 20px;
        border: 2px solid #ddd;
        border-radius: 10px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .viewer-option:hover {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .viewer-option.active {
        border-color: #007bff;
        background: #e6f3ff;
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
                        <li><i class="fas fa-check text-success me-1"></i> تكبير/تصغير بالماوس</li>
                        <li><i class="fas fa-check text-success me-1"></i> التنقل بأسهم لوحة المفاتيح</li>
                        <li><i class="fas fa-check text-success me-1"></i> البحث داخل النص بـ Ctrl+F</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- PDF Viewer -->
        <div class="col-lg-9" id="viewer-container">
            <div class="reader-controls">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">{{ $book->title }}</h5>
                        <small class="text-muted">بواسطة {{ $book->author }}</small>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-secondary" onclick="switchViewer('iframe')">
                                <i class="fas fa-window-maximize"></i> عارض مدمج
                            </button>
                            <button class="btn btn-outline-secondary" onclick="switchViewer('direct')">
                                <i class="fas fa-external-link-alt"></i> فتح مباشر
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pdf-viewer-container" id="main-viewer">
                <!-- عارض iframe -->
                <div id="iframe-viewer" style="width: 100%; height: 100%;">
                    <iframe 
                        id="pdf-viewer"
                        class="pdf-viewer" 
                        src="{{ route('books.serve', $book) }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH&pagemode=none"
                        onload="disablePrint()"
                        oncontextmenu="return false;"
                        allow="fullscreen"
                        loading="lazy">
                        <p>متصفحك لا يدعم عرض ملفات PDF.</p>
                    </iframe>
                </div>
                
                <!-- عارض بديل -->
                <div id="alternative-viewer" class="alternative-viewer" style="display: none;">
                    <div class="text-center">
                        <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        <h4>اختر طريقة عرض الكتاب</h4>
                        <p class="text-muted mb-4">في حالة عدم ظهور الكتاب في العارض المدمج</p>
                        
                        <div class="viewer-option" onclick="openInNewTab()">
                            <i class="fas fa-external-link-alt fa-2x text-primary mb-2"></i>
                            <h6>فتح في نافذة جديدة</h6>
                            <small class="text-muted">يفتح الكتاب في تبويب منفصل</small>
                        </div>
                        
                        <div class="viewer-option" onclick="tryIframeAgain()">
                            <i class="fas fa-redo fa-2x text-success mb-2"></i>
                            <h6>إعادة تحميل العارض</h6>
                            <small class="text-muted">محاولة عرض الكتاب مرة أخرى</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let sidebarVisible = true;
let isFullscreen = false;

// منع الطباعة وحفظ الصفحة
document.addEventListener('keydown', function(e) {
    // منع Ctrl+P (طباعة)
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        showMessage('الطباعة غير مسموحة', 'error');
        return false;
    }
    
    // منع Ctrl+S (حفظ)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        showMessage('حفظ الملف غير مسموح', 'error');
        return false;
    }
    
    // منع F12 (أدوات المطور)
    if (e.key === 'F12') {
        e.preventDefault();
        showMessage('أدوات المطور غير متاحة', 'error');
        return false;
    }
    
    // منع Ctrl+Shift+I (أدوات المطور)
    if (e.ctrlKey && e.shiftKey && e.key === 'I') {
        e.preventDefault();
        showMessage('أدوات المطور غير متاحة', 'error');
        return false;
    }
    
    // منع Ctrl+U (عرض المصدر)
    if (e.ctrlKey && e.key === 'u') {
        e.preventDefault();
        showMessage('عرض المصدر غير مسموح', 'error');
        return false;
    }
    
    // منع Ctrl+Shift+C (فحص العنصر)
    if (e.ctrlKey && e.shiftKey && e.key === 'C') {
        e.preventDefault();
        showMessage('فحص العنصر غير مسموح', 'error');
        return false;
    }
    
    // منع Ctrl+A (تحديد الكل)
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        showMessage('تحديد النص غير مسموح', 'error');
        return false;
    }
});

// منع النقر بالزر الأيمن
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    showMessage('النقر بالزر الأيمن غير مسموح', 'warning');
    return false;
});

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

function switchViewer(type) {
    const iframeViewer = document.getElementById('iframe-viewer');
    const alternativeViewer = document.getElementById('alternative-viewer');
    
    if (type === 'iframe') {
        iframeViewer.style.display = 'block';
        alternativeViewer.style.display = 'none';
    } else {
        iframeViewer.style.display = 'none';
        alternativeViewer.style.display = 'flex';
    }
}

function openInNewTab() {
    window.open('{{ route("books.serve", $book) }}', '_blank');
    showMessage('تم فتح الكتاب في نافذة جديدة', 'success');
}

function tryIframeAgain() {
    const iframe = document.getElementById('pdf-viewer');
    iframe.src = iframe.src;
    switchViewer('iframe');
    showMessage('جاري إعادة تحميل العارض...', 'info');
}

function disablePrint() {
    const iframe = document.getElementById('pdf-viewer');
    try {
        if (iframe.contentWindow) {
            iframe.contentWindow.print = function() {
                showMessage('الطباعة غير مسموحة', 'error');
                return false;
            };
        }
    } catch (e) {
        // Cross-origin restrictions
    }
}

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

// تتبع تقدم القراءة (تقديري)
let readingStartTime = Date.now();
setInterval(function() {
    const timeElapsed = Date.now() - readingStartTime;
    const progressPercent = Math.min((timeElapsed / (1000 * 60 * 10)) * 100, 100); // 10 دقائق = 100%
    document.getElementById('reading-progress').style.width = progressPercent + '%';
}, 5000);

// منع نسخ النص
document.addEventListener('copy', function(e) {
    e.preventDefault();
    showMessage('نسخ النص غير مسموح', 'warning');
    return false;
});

// إضافة watermark شفاف والتحقق من iframe
window.addEventListener('load', function() {
    const watermark = document.createElement('div');
    watermark.innerHTML = '{{ config("app.name") }} - {{ $book->title }}';
    watermark.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 48px;
        color: rgba(0,0,0,0.1);
        pointer-events: none;
        z-index: 999;
        user-select: none;
    `;
    document.body.appendChild(watermark);
    
    // التحقق من تحميل iframe
    const iframe = document.getElementById('pdf-viewer');
    let iframeLoaded = false;
    let loadAttempts = 0;
    
    iframe.addEventListener('load', function() {
        iframeLoaded = true;
        try {
            // التحقق من أن المحتوى محمل بشكل صحيح
            if (iframe.contentDocument || iframe.contentWindow) {
                showMessage('تم تحميل الكتاب بنجاح', 'success');
            }
        } catch(e) {
            // Cross-origin restrictions - هذا طبيعي
        }
    });
    
    iframe.addEventListener('error', function() {
        loadAttempts++;
        if (loadAttempts > 2) {
            showMessage('مشكلة في تحميل الكتاب، جاري التبديل للعارض البديل', 'warning');
            setTimeout(() => switchViewer('direct'), 2000);
        }
    });
    
    // إذا لم يتم تحميل iframe خلال 8 ثوانٍ، إظهار البديل
    setTimeout(function() {
        if (!iframeLoaded) {
            showMessage('يستغرق تحميل الكتاب وقتاً أطول من المعتاد', 'info');
            setTimeout(() => {
                if (!iframeLoaded) {
                    switchViewer('direct');
                }
            }, 5000);
        }
    }, 8000);
});
</script>
@endpush
@endsection
