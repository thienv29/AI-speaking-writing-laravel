<header class="bg-[#662872] px-8 py-3 flex items-center justify-between shadow-md">
  <!-- Logo -->
  <div class="flex items-center">
    <a href="{{ url('/') }}" class="block">
        <img 
        src="https://i-clc.edu.vn/wp-content/uploads/2024/10/LOGO-ICLC.png" 
        alt="Logo" 
        class="h-14 w-auto cursor-pointer"
        />
    </a>
  </div>

  <!-- Menu -->
  <nav class="flex space-x-8 text-white font-semibold text-lg">
    <a href="{{ url('/') }}" class=" menu-item hover:text-yellow-400">Trang chủ</a>
    <a href="#" class="menu-item hover:text-yellow-400">Giới thiệu</a>
    <a href="{{ url('/lessons') }}" class="menu-item hover:text-yellow-400">Bài học</a>
    <a href="#" class="menu-item hover:text-yellow-400">Chính sách</a>
    <a href="#" class="menu-item hover:text-yellow-400">Tin tức</a>
    <a href="#" class="menu-item hover:text-yellow-400">Liên hệ</a>
  </nav>
</header>
