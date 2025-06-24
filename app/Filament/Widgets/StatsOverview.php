<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الكتب', \App\Models\Book::count())
                ->description('جميع الكتب في المكتبة')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('الكتب النشطة', \App\Models\Book::where('is_active', true)->count())
                ->description('الكتب المتاحة للعرض')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('إجمالي الفئات', \App\Models\Category::count())
                ->description('جميع فئات الكتب')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),

            Stat::make('إجمالي المشاهدات', \App\Models\Book::sum('view_count'))
                ->description('مجموع مشاهدات جميع الكتب')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
        ];
    }
}
