import './bootstrap';

if (document.getElementById('question-page-content') || document.getElementsByClassName('embed-compact')) {
    import('./user/questionPage/writing-feedback.js');
    import('./user/questionPage/main.js');
}

if (document.getElementById('admin-exercises-page-content')) {
    import('./admin/exercises/importExcel.js');
}

