<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'مكتبة الكتب الإلكترونية')</title>
    <meta name="description" content="@yield('description', 'مكتبة شاملة للكتب الإلكترونية بصيغة PDF - اقرأ وحمل آلاف الكتب مجاناً')">
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Noto Sans Arabic) -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }
        
        .search-box:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .book-card {
            height: 100%;
        }
        
        .book-cover {
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .category-card {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            transform: scale(1.05);
            text-decoration: none;
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 500;
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 500;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 50px 0 20px;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .badge-custom {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            color: white;
            border-radius: 50px;
            padding: 5px 15px;
        }
        
        .nav-link:hover {
            color: #667eea !important;
        }
        
        .navbar-toggler {
            border: none;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
          .rating {
            color: #ffc107;
        }
        
        .rating .fa-star {
            color: #ffc107;
        }
        
        .rating .fa-star.empty {
            color: #e4e5e9;
        }
        
        .book-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .book-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ route('home') }}">
                <i class="fas fa-book-open me-2"></i>
                مكتبة الكتب
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('books.index') }}">جميع الكتب</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            الفئات
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}">جميع الفئات</a></li>
                            <li><hr class="dropdown-divider"></li>                            @foreach($headerCategories as $category)
                                <li><a class="dropdown-item" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" method="GET" action="{{ route('search') }}">
                    <div class="input-group">
                        <input class="form-control" type="search" name="search" placeholder="ابحث عن كتاب..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Admin Link -->
                <a href="/admin" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-cog me-1"></i>
                    لوحة الإدارة
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-book-open me-2"></i>
                        مكتبة الكتب
                    </h5>
                    <p class="text-light">
                        مكتبة شاملة للكتب الإلكترونية بصيغة PDF. اكتشف آلاف الكتب في مختلف المجالات واقرأها أو حملها مجاناً.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">الرئيسية</a></li>
                        <li><a href="{{ route('books.index') }}" class="text-light text-decoration-none">جميع الكتب</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-light text-decoration-none">الفئات</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="mb-3">الفئات الشائعة</h6>
                    <ul class="list-unstyled">
                        @foreach($headerCategories->take(5) as $category)
                            <li><a href="{{ route('categories.show', $category) }}" class="text-light text-decoration-none">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6 class="mb-3">تواصل معنا</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light">&copy; {{ date('Y') }} مكتبة الكتب. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light">تم التطوير بـ <i class="fas fa-heart text-danger"></i> باستخدام Laravel</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
