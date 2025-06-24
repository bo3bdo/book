<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDirectPdfAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */    public function handle(Request $request, Closure $next)
    {
        // السماح بالوصول من صفحة القارئ
        $referer = $request->header('referer');

        // إذا كان هناك referer ويحتوي على "/view" أو كان من نفس الموقع، السماح بالوصول
        if ($referer && (str_contains($referer, '/view') || str_contains($referer, $request->getHost()))) {
            return $next($request);
        }

        // السماح أيضاً إذا كان الطلب من iframe
        if ($request->header('sec-fetch-dest') === 'iframe') {
            return $next($request);
        }

        // منع التحميل المباشر من أدوات التحميل
        $userAgent = strtolower($request->header('User-Agent', ''));
        if (
            str_contains($userAgent, 'wget') ||
            str_contains($userAgent, 'curl') ||
            str_contains($userAgent, 'download')
        ) {
            abort(403, 'تحميل الملف غير مسموح');
        }

        return $next($request);
    }
}
