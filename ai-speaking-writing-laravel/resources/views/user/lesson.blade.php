@extends('layouts.main')

@section('title', 'Question - AI Speaking - Writing Laravel')

@section('content')
  <!-- Header thông tin lesson -->
  <div class="bg-[#f9f5fc] rounded-2xl shadow p-6 flex flex-col md:flex-row gap-6 mb-8">
    <img id="lesson-img" src="https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg"
      alt="Lesson image" class="w-full md:w-1/3 h-56 object-contain rounded-xl">
    
    <div class="flex-1 flex flex-col justify-between">
      <div>
        <h1 id="lesson-title" class="text-2xl font-bold text-gray-800 mb-2"></h1>
        <span id="lesson-difficulty"
          class="font-semibold inline-block bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm"></span>
        <p id="lesson-desc" class="text-gray-600 mt-4">
        </p>
      </div>

      <div class="mt-6 flex gap-4">
        <button
          id="start-lesson-btn"
          class="mt-4 bg-[#ffe7ea] border-2 border-[#f6a914] text-[#f6a914] hover:text-white hover:bg-[#f6a914] font-medium px-4 py-2 rounded-full transition font-semibold text-lg">Bắt đầu học</button>
      </div>
    </div>
  </div>

  <!-- Danh sách Exercise -->
  <div id="exerciseList" class="rounded"></div>

  <script>
    window.appData = {
        lessonId: {{ $id }}
    };
  </script>

@endsection