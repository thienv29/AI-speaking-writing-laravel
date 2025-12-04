@extends('layouts.app')

@section('meta')
<title>Review Câu Trả Lời | I-CLC</title>
@endsection

@section('navigation')
    <a href="/">Trang chủ</a>
    <a href="/writing">Luyện viết</a>
    <a href="/#contact">Liên hệ</a>
@endsection

{{-- @push('styles')
<link rel="stylesheet" href="/css/review.css">
@endpush --}}

@section('content')
<div class="review-page-wrapper">
    <div class="container">
        <div class="review-header">
            <h1>📚 Review Câu Trả Lời Của Học Sinh</h1>
            <p class="review-subtitle">Xem lại các câu trả lời và feedback của học sinh</p>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('review.index') }}" class="filters-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Học sinh:</label>
                        <select name="user_id" class="filter-select">
                            <option value="">Tất cả học sinh</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Bài học & Bài tập:</label>
                        <select name="lesson_id" id="lessonSelect" class="filter-select">
                            <option value="">Tất cả bài học</option>
                            @foreach($lessons as $lesson)
                                <optgroup label="📚 {{ $lesson->title }}">
                                    <option value="lesson_{{ $lesson->id }}" {{ request('lesson_id') == 'lesson_' . $lesson->id ? 'selected' : '' }}>
                                        📚 {{ $lesson->title }} (Tất cả bài tập)
                                    </option>
                                    @foreach($lesson->exercises as $exercise)
                                        <option value="exercise_{{ $exercise->id }}" {{ request('lesson_id') == 'exercise_' . $exercise->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;✏️ {{ $exercise->title }} ({{ $exercise->type->code ?? '' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Kết quả:</label>
                        <select name="is_correct" class="filter-select">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('is_correct') === '1' ? 'selected' : '' }}>Đúng</option>
                            <option value="0" {{ request('is_correct') === '0' ? 'selected' : '' }}>Sai</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">🔍 Lọc</button>
                        <a href="{{ route('review.index') }}" class="btn-reset">🔄 Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Attempts Table -->
        <div class="attempts-table-card">
            <div class="table-header">
                <h2>Danh sách câu trả lời ({{ $attempts->total() }} câu)</h2>
            </div>

            @if($attempts->count() > 0)
                <div class="table-container">
                    <table class="attempts-table">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Học sinh</th>
                                <th>Bài học</th>
                                <th>Bài tập</th>
                                <th>Câu hỏi</th>
                                <th>Câu trả lời</th>
                                <th>Kết quả</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td class="time-cell">
                                        {{ $attempt->created_at ? \Carbon\Carbon::parse($attempt->created_at)->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td class="user-cell">
                                        <strong>{{ $attempt->user->name ?? 'Guest' }}</strong>
                                        <br>
                                        <small>{{ $attempt->user->email ?? '' }}</small>
                                    </td>
                                    <td class="lesson-cell">
                                        @php
                                            $exercise = $attempt->question->exercises->first();
                                        @endphp
                                        {{ $exercise->lesson->title ?? '—' }}
                                    </td>
                                    <td class="exercise-cell">
                                        @php
                                            $exercise = $attempt->question->exercises->first();
                                        @endphp
                                        {{ $exercise->title ?? '—' }}
                                        <br>
                                        <small class="type-badge">{{ $exercise->type->code ?? '' }}</small>
                                    </td>
                                    <td class="question-cell">
                                        {{ Str::limit($attempt->question->prompt_text ?? '—', 50) }}
                                    </td>
                                    <td class="answer-cell">
                                        <div class="answer-text">{{ $attempt->user_answer ?? '—' }}</div>
                                    </td>
                                    <td class="result-cell">
                                        @if($attempt->is_correct)
                                            <span class="badge-success">✅ Đúng</span>
                                        @else
                                            <span class="badge-error">❌ Sai</span>
                                        @endif
                                    </td>
                                    <td class="feedback-cell">
                                        <div class="feedback-text">{{ Str::limit($attempt->feedback ?? '—', 100) }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $attempts->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <h3>Chưa có câu trả lời nào</h3>
                    <p>Học sinh chưa làm bài tập nào.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto submit form on filter change
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function() {
            // Optional: auto-submit on change
            // this.closest('form').submit();
        });
    });
</script>
@endpush

