@extends('layouts.main')

@section('title', 'Question - AI Speaking - Writing Laravel')

@section('content')
  <!-- Thanh tìm kiếm + lọc + sắp xếp -->
  <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
    <!-- Ô tìm kiếm -->
    <div class="flex items-center bg-white shadow rounded px-4 py-2 w-full sm:w-1/3">
      <input id="searchInput" type="text" placeholder="Tìm bài học..."
        class="flex-1 outline-none text-gray-700" />
      <i class="bi bi-search"></i>
    </div>

    <div class="flex gap-6">
        <!-- Bộ lọc -->
        <select id="filterSelect" class="bg-white shadow rounded px-4 py-2 text-gray-700">
            <option value="">Tất cả độ khó</option>
            <option value="Dễ">Dễ</option>
            <option value="Trung bình">Trung bình</option>
            <option value="Khó">Khó</option>
        </select>

        <!-- Sắp xếp -->
        <select id="sortSelect" class="bg-white shadow rounded px-4 py-2 text-gray-700">
            <option value="newest">Mới nhất</option>
            <option value="oldest">Cũ nhất</option>
            <option value="az">A → Z</option>
            <option value="za">Z → A</option>
        </select>
    </div>
  </div>

  <!-- Danh sách bài học -->
  <div id="lessonList" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
    <!-- Các thẻ bài học sẽ render ở đây -->
  </div>
@endsection