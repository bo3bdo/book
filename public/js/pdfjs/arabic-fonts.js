// خطوط عربية محلية لدعم النصوص العربية في PDF.js
// هذه الخطوط تدعم الأحرف العربية بشكل كامل وتعمل بدون إنترنت

window.ArabicFonts = {
    // تسجيل جميع الخطوط العربية المتاحة
    register: function() {
        try {
            // إضافة CSS للخطوط العربية
            const style = document.createElement('style');
            style.textContent = `                /* خط أمیری العربي المحلي */
                @font-face {
                    font-family: 'AmiriLocal';
                    src: url('/book/public/js/pdfjs/amiri-arabic.woff2') format('woff2');
                    font-weight: normal;
                    font-style: normal;
                    font-display: swap;
                }
                
                /* خط عربي احتياطي */
                @font-face {
                    font-family: 'ArabicFallback';
                    src: local('Arial'), local('Tahoma'), local('Segoe UI');
                    unicode-range: U+0600-06FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE80-FEFC;
                }
                
                /* فئات CSS للنصوص العربية */
                .arabic-text {
                    font-family: 'AmiriLocal', 'ArabicFallback', 'Arial', 'Tahoma', sans-serif !important;
                    direction: rtl !important;
                    unicode-bidi: bidi-override !important;
                    text-align: right !important;
                    font-feature-settings: 'liga', 'kern', 'mark', 'mkmk';
                    -webkit-font-feature-settings: 'liga', 'kern', 'mark', 'mkmk';
                }
                
                .pdf-arabic {
                    font-family: 'AmiriLocal', 'ArabicFallback', 'Arial', 'Tahoma', sans-serif !important;
                    direction: rtl !important;
                    text-rendering: optimizeLegibility;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }
                
                /* تحسين Canvas للنصوص العربية */
                canvas {
                    font-family: 'AmiriLocal', 'ArabicFallback', 'Arial', 'Tahoma', sans-serif !important;
                    direction: rtl;
                    unicode-bidi: bidi-override;
                }
            `;
            document.head.appendChild(style);
            
            console.log('تم تسجيل الخطوط العربية المحلية بنجاح');
            return true;
        } catch (error) {
            console.error('خطأ في تسجيل الخطوط العربية:', error);
            return false;
        }
    },
    
    // تحميل الخطوط وانتظار جاهزيتها
    load: async function() {
        try {
            // انتظار تحميل الخطوط
            await document.fonts.ready;
            
            // اختبار وجود الخط العربي
            const testElement = document.createElement('span');
            testElement.style.fontFamily = 'AmiriLocal';
            testElement.textContent = 'ا';
            document.body.appendChild(testElement);
            
            const computedFont = window.getComputedStyle(testElement).fontFamily;
            document.body.removeChild(testElement);
            
            if (computedFont.includes('AmiriLocal')) {
                console.log('خط أمیری العربي جاهز للاستخدام');
                return true;
            } else {
                console.log('سيتم استخدام الخط الاحتياطي');
                return false;
            }
        } catch (error) {
            console.error('خطأ في تحميل الخطوط:', error);
            return false;
        }
    }
};

// دالة لتطبيق الخطوط العربية على PDF.js
window.enableArabicFontsForPDF = async function() {
    try {
        // تسجيل الخطوط
        ArabicFonts.register();
        
        // انتظار تحميل الخطوط
        await ArabicFonts.load();
        
        // إعداد PDF.js لاستخدام الخطوط العربية
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.disableFontFace = false;
            
            // إعداد خيارات إضافية للنصوص العربية
            const originalGetDocument = pdfjsLib.getDocument;
            pdfjsLib.getDocument = function(src) {
                if (typeof src === 'object') {
                    src.fontExtraProperties = true;
                    src.disableFontFace = false;
                    src.enableXfa = false;
                }
                return originalGetDocument(src);
            };
            
            console.log('تم تفعيل دعم الخطوط العربية في PDF.js');
        }
        
        return true;
    } catch (error) {
        console.error('خطأ في تفعيل الخطوط العربية:', error);
        return false;
    }
};

// تشغيل تلقائي عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    enableArabicFontsForPDF();
});

console.log('ملف الخطوط العربية المحسّن محمّل ومتاح');
