@extends('layouts.main')

@section('title', 'Trang chủ - AI Speaking English')

@section('content')
<div class="text-center bg-[#f9f5fc]">
    <h2 class="text-3xl font-bold text-[#f6a914] mb-4">
        Chào mừng bạn đến với AI Speaking - Writing Laravel 👋
    </h2>
    <p class="text-lg text-gray-600 mb-6">
        Nền tảng luyện nói tiếng Anh cùng trí tuệ nhân tạo.
    </p>
    <a
        href="{{ url('/lessons') }}"
        id="lesson-btn"
        class="mt-4 bg-[#ffe7ea] border-2 border-[#f6a914] text-[#f6a914] hover:text-white hover:bg-[#f6a914] font-medium px-4 py-2 rounded-full transition font-semibold text-lg">
        Bắt đầu ngay
    </a>
</div>
@endsection
