import './bootstrap';

if (window.location.pathname.includes('/questions')) {
    import('./user/questionPage.js').then(module => {
        console.log('✅ questionPage.js đã load');
    }).catch(err => console.error('❌ Lỗi load questionPage.js:', err));
}

if (/^\/lessons(\/([^0-9].*)?)?$/.test(window.location.pathname)) {
    import('./user/lessonsPage.js').then(module => {
        console.log('✅ lessonsPage.js đã load');
    }).catch(err => console.error('❌ Lỗi load lessonsPage.js:', err));
}

if (/\/lessons\/\d+/.test(window.location.pathname))  {
    import('./user/lessonPage.js').then(module => {
        console.log('✅ lessonPage.js đã load');
    }).catch(err => console.error('❌ Lỗi load lessonPage.js:', err));
}

console.log("✅ Vite đã hoạt động trong Laravel 8!");