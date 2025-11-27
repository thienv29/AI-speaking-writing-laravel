@extends('admin.layout')

@section('title', 'Quản lý Câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Câu hỏi</h1>
            <p class="mt-2 text-gray-600">Danh sách tất cả câu hỏi trong hệ thống</p>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tạo câu hỏi mới
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Tìm theo đề bài..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label for="lesson_id" class="block text-sm font-medium text-gray-700 mb-2">Bài học</label>
                <select id="lesson_id" name="lesson_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[180px]">
                <label for="exercise_id" class="block text-sm font-medium text-gray-700 mb-2">Bài tập</label>
                <select id="exercise_id" name="exercise_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}" {{ request('exercise_id') == $exercise->id ? 'selected' : '' }}>
                            {{ $exercise->title }} ({{ $exercise->type->code ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
                <select id="type_id" name="type_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[180px]">
                <label for="group_id" class="block text-sm font-medium text-gray-700 mb-2">Nhóm</label>
                <select id="group_id" name="group_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lọc
                </button>
                <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span id="selectedCount" class="text-sm font-medium text-gray-700">Đã chọn: <strong>0</strong> câu hỏi</span>
                <form id="bulkAssignForm" method="POST" action="{{ route('admin.questions.bulk-assign-group') }}" class="flex items-center gap-2">
                    @csrf
                    <label for="bulkGroupSelect" class="text-sm font-medium text-gray-700">Gán vào nhóm (có thể chọn nhiều):</label>
                    <select id="bulkGroupSelect" name="group_ids[]" multiple class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" size="4" style="min-width: 200px;">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Áp dụng
                    </button>
                    <button type="button" onclick="clearBulkGroups()" class="px-4 py-1.5 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600">
                        Xóa nhóm
                    </button>
                </form>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.questions.bulk-delete') }}" onsubmit="return confirmBulkDelete()">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        Xóa đã chọn
                    </button>
                </form>
            </div>
            <button onclick="clearSelection()" class="text-sm text-gray-600 hover:text-gray-800">
                Bỏ chọn tất cả
            </button>
        </div>
    </div>

    <div>
        @csrf
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đề bài</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài tập</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhóm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($questions as $question)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" 
                                class="question-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                onchange="updateBulkActionsBar()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->order_index ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($question->prompt_text, 50) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($question->exercises->count())
                                <div class="flex flex-col gap-1">
                                    @foreach($question->exercises as $exercise)
                                        <div class="flex items-center justify-between gap-2">
                                            <span>{{ $exercise->title }}</span>
                                            <span class="text-xs text-gray-400">
                                                {{ $exercise->lesson->title ?? '' }} • {{ $exercise->type->code ?? '' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($question->groups->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($question->groups as $group)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                            {{ $group->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.questions.show', $question) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                                <a href="{{ route('admin.questions.edit', $question) . '?' . http_build_query(request()->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page'])) }}" class="text-yellow-600 hover:text-yellow-900">Sửa</a>
                                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?');">
                                    @csrf
                                    @method('DELETE')
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    @if(request('lesson_id'))
                                        <input type="hidden" name="lesson_id" value="{{ request('lesson_id') }}">
                                    @endif
                                    @if(request('exercise_id'))
                                        <input type="hidden" name="exercise_id" value="{{ request('exercise_id') }}">
                                    @endif
                                    @if(request('type_id'))
                                        <input type="hidden" name="type_id" value="{{ request('type_id') }}">
                                    @endif
                                    @if(request('page'))
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                    @endif
                                    <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có câu hỏi nào</td>
                    </tr>
                    @endforelse
            </tbody>
        </table>
    </div>
    </div>

    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActionsBar();
    }

    function updateBulkActionsBar() {
        const checkboxes = document.querySelectorAll('.question-checkbox:checked');
        const count = checkboxes.length;
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        
        if (count > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.innerHTML = `Đã chọn: <strong>${count}</strong> câu hỏi`;
        } else {
            bulkBar.classList.add('hidden');
        }
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.question-checkbox');
        const selectAll = document.getElementById('selectAll');
        if (allCheckboxes.length > 0) {
            selectAll.checked = checkboxes.length === allCheckboxes.length;
            selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
        }
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActionsBar();
    }

    function confirmBulkDelete() {
        const checkboxes = document.querySelectorAll('.question-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một câu hỏi!');
            return false;
        }
        return confirm(`Bạn có chắc muốn xóa ${checkboxes.length} câu hỏi đã chọn?`);
    }

    // Handle form submissions
    document.addEventListener('DOMContentLoaded', function() {
        const bulkAssignForm = document.getElementById('bulkAssignForm');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        if (bulkAssignForm) {
            bulkAssignForm.addEventListener('submit', function(e) {
                const checkboxes = document.querySelectorAll('.question-checkbox:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một câu hỏi!');
                    return false;
                }

                // Add selected question IDs to form
                checkboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'question_ids[]';
                    input.value = checkbox.value;
                    bulkAssignForm.appendChild(input);
                });
                
                // Ensure group_ids[] is sent even if empty
                const groupSelect = document.getElementById('bulkGroupSelect');
                if (groupSelect.selectedOptions.length === 0) {
                    // If no groups selected, we'll send empty array to remove all groups
                    const emptyInput = document.createElement('input');
                    emptyInput.type = 'hidden';
                    emptyInput.name = 'group_ids[]';
                    emptyInput.value = '';
                    bulkAssignForm.appendChild(emptyInput);
                }

                // Add filter params
                @if(request('search'))
                    const searchInput = document.createElement('input');
                    searchInput.type = 'hidden';
                    searchInput.name = 'search';
                    searchInput.value = '{{ request('search') }}';
                    bulkAssignForm.appendChild(searchInput);
                @endif
                @if(request('lesson_id'))
                    const lessonInput = document.createElement('input');
                    lessonInput.type = 'hidden';
                    lessonInput.name = 'lesson_id';
                    lessonInput.value = '{{ request('lesson_id') }}';
                    bulkAssignForm.appendChild(lessonInput);
                @endif
                @if(request('exercise_id'))
                    const exerciseInput = document.createElement('input');
                    exerciseInput.type = 'hidden';
                    exerciseInput.name = 'exercise_id';
                    exerciseInput.value = '{{ request('exercise_id') }}';
                    bulkAssignForm.appendChild(exerciseInput);
                @endif
                @if(request('type_id'))
                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type_id';
                    typeInput.value = '{{ request('type_id') }}';
                    bulkAssignForm.appendChild(typeInput);
                @endif
                @if(request('page'))
                    const pageInput = document.createElement('input');
                    pageInput.type = 'hidden';
                    pageInput.name = 'page';
                    pageInput.value = '{{ request('page') }}';
                    bulkAssignForm.appendChild(pageInput);
                @endif
            });
        }

        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function(e) {
                const checkboxes = document.querySelectorAll('.question-checkbox:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một câu hỏi!');
                    return false;
                }

                // Add selected question IDs to form
                checkboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'question_ids[]';
                    input.value = checkbox.value;
                    bulkDeleteForm.appendChild(input);
                });

                // Add filter params
                @if(request('search'))
                    const searchInput = document.createElement('input');
                    searchInput.type = 'hidden';
                    searchInput.name = 'search';
                    searchInput.value = '{{ request('search') }}';
                    bulkDeleteForm.appendChild(searchInput);
                @endif
                @if(request('lesson_id'))
                    const lessonInput = document.createElement('input');
                    lessonInput.type = 'hidden';
                    lessonInput.name = 'lesson_id';
                    lessonInput.value = '{{ request('lesson_id') }}';
                    bulkDeleteForm.appendChild(lessonInput);
                @endif
                @if(request('exercise_id'))
                    const exerciseInput = document.createElement('input');
                    exerciseInput.type = 'hidden';
                    exerciseInput.name = 'exercise_id';
                    exerciseInput.value = '{{ request('exercise_id') }}';
                    bulkDeleteForm.appendChild(exerciseInput);
                @endif
                @if(request('type_id'))
                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = 'type_id';
                    typeInput.value = '{{ request('type_id') }}';
                    bulkDeleteForm.appendChild(typeInput);
                @endif
                @if(request('page'))
                    const pageInput = document.createElement('input');
                    pageInput.type = 'hidden';
                    pageInput.name = 'page';
                    pageInput.value = '{{ request('page') }}';
                    bulkDeleteForm.appendChild(pageInput);
                @endif
            });
        }
    });

    function clearBulkGroups() {
        const groupSelect = document.getElementById('bulkGroupSelect');
        Array.from(groupSelect.options).forEach(option => {
            option.selected = false;
        });
    }
</script>
@endsection
