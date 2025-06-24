<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'الكتب';

    protected static ?string $modelLabel = 'كتاب';

    protected static ?string $pluralModelLabel = 'الكتب';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الكتاب الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان الكتاب')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('وصف الكتاب')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('author')
                            ->label('المؤلف')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('الفئة')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('تفاصيل النشر')
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->label('رقم ISBN')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('pages')
                            ->label('عدد الصفحات')
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\Select::make('language')
                            ->label('اللغة')
                            ->options([
                                'ar' => 'العربية',
                                'en' => 'الإنجليزية',
                                'fr' => 'الفرنسية',
                                'es' => 'الإسبانية',
                                'other' => 'أخرى',
                            ])
                            ->default('ar')
                            ->required(),

                        Forms\Components\TextInput::make('publication_year')
                            ->label('سنة النشر')
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(date('Y')),

                        Forms\Components\TextInput::make('publisher')
                            ->label('دار النشر')
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('الملفات والوسائط')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('صورة الغلاف')
                            ->image()
                            ->directory('books/covers')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageCropAspectRatio('3:4'),

                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('ملف PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('books/pdfs')
                            ->visibility('public')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('الإعدادات')
                    ->schema([
                        Forms\Components\TextInput::make('rating')
                            ->label('التقييم')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1)
                            ->default(0),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('كتاب مميز')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('الغلاف')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('author')
                    ->label('المؤلف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('الفئة')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('language')
                    ->label('اللغة')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'ar' => 'العربية',
                        'en' => 'الإنجليزية',
                        'fr' => 'الفرنسية',
                        'es' => 'الإسبانية',
                        default => 'أخرى',
                    }),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('التحميلات')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('view_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('التقييم')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميز')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('الفئة')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('language')
                    ->label('اللغة')
                    ->options([
                        'ar' => 'العربية',
                        'en' => 'الإنجليزية',
                        'fr' => 'الفرنسية',
                        'es' => 'الإسبانية',
                        'other' => 'أخرى',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('مميز')
                    ->boolean()
                    ->trueLabel('مميز فقط')
                    ->falseLabel('غير مميز فقط')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
