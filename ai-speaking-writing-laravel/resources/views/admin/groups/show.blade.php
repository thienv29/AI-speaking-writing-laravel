@extends('admin.layout')

@section('title', 'Chi tiết Nhóm: ' . $group->name)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $group->name }}</h1>
                <p class="mt-2 text-gray-600">ID: {{ $group->id }} • Tạo: {{ $group->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.groups.edit', $group) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Chỉnh sửa nhóm
                </a>
                <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    ← Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">Q</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Tổng câu hỏi</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $group->questions->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">T</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Loại bài tập</h3>
                    <p class="text-sm text-gray-600">{{ $group->questions->pluck('exercise.type.code')->unique()->join(', ') ?: 'Chưa có' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">L</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Bài học liên quan</h3>
                    <p class="text-sm text-gray-600">{{ $group->questions->pluck('exercise.lesson.title')->unique()->join(', ') ?: 'Chưa có' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Management -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Danh sách câu hỏi</h2>
                <button onclick="showAddQuestionModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Thêm câu hỏi
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài học</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($group->questions as $question)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate">
                                @if($question->img_url)
                                    <span class="text-blue-600">[Hình ảnh]</span>
                                @endif
                                {{ Str::limit($question->target_text ?? $question->starter_text, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($question->exercise->type->code == 'SPW') bg-blue-100 text-blue-800
                                @elseif($question->exercise->type->code == 'SPS') bg-green-100 text-green-800
                                @elseif($question->exercise->type->code == 'WAQ') bg-yellow-100 text-yellow-800
                                @elseif($question->exercise->type->code == 'WCS') bg-purple-100 text-purple-800
                                @elseif($question->exercise->type->code == 'WSG') bg-pink-100 text-pink-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $question->exercise->type->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($question->exercise->lesson->title, 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.groups.remove-question', [$group, $question->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa câu hỏi khỏi nhóm?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có câu hỏi nào trong nhóm</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="my-modal">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Thêm câu hỏi vào nhóm</h3>
                <button onclick="closeAddQuestionModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.groups.add-question', $group) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="question_search" class="block text-sm font-medium text-gray-700 mb-2">Tìm câu hỏi</label>
                    <input type="text" id="question_search" placeholder="Nhập ID câu hỏi hoặc từ khóa..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div id="questions_list" class="max-h-60 overflow-y-auto border rounded-lg p-2 mb-4">
                    <p class="text-sm text-gray-500">Nhập từ khóa để tìm câu hỏi...</p>
                </div>

                <input type="hidden" name="question_id" id="selected_question_id">

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddQuestionModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" id="add_question_btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" disabled>
                        Thêm vào nhóm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddQuestionModal() {
    document.getElementById('addQuestionModal').classList.remove('hidden');
}

function closeAddQuestionModal() {
    document.getElementById('addQuestionModal').classList.add('hidden');
    document.getElementById('question_search').value = '';
    document.getElementById('questions_list').innerHTML = '<p class="text-sm text-gray-500">Nhập từ khóa để tìm câu hỏi...</p>';
    document.getElementById('selected_question_id').value = '';
    document.getElementById('add_question_btn').disabled = true;
}

document.getElementById('question_search').addEventListener('input', function() {
    const searchTerm = this.value.trim();

    if (searchTerm.length < 2) {
        document.getElementById('questions_list').innerHTML = '<p class="text-sm text-gray-500">Nhập ít nhất 2 ký tự...</p>';
        return;
    }

    fetch(`/admin/questions/search?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';

            if (data.length === 0) {
                html = '<p class="text-sm text-gray-500">Không tìm thấy câu hỏi nào</p>';
            } else {
                data.forEach(question => {
                    html += `
                        <div class="p-2 border-b cursor-pointer hover:bg-gray-50" onclick="selectQuestion(${question.id}, '${question.text}')">
                            <div class="font-medium">ID: ${question.id}</div>
                            <div class="text-sm text-gray-600">${question.text}</div>
                            <div class="text-xs text-gray-500">${question.type} - ${question.lesson}</div>
                        </div>
                    `;
                });
            }

            document.getElementById('questions_list').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('questions_list').innerHTML = '<p class="text-sm text-red-500">Lỗi khi tìm kiếm</p>';
        });
});

function selectQuestion(id, text) {
    document.getElementById('selected_question_id').value = id;
    document.getElementById('add_question_btn').disabled = false;

    // Highlight selected question
    const questions = document.querySelectorAll('#questions_list > div');
    questions.forEach(q => q.classList.remove('bg-blue-50'));
    event.target.closest('div').classList.add('bg-blue-50');
}
</script>
@endsection
