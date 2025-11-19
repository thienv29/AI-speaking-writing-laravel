import exerciseApi from '../../api/admin/exerciseApi';

const convertBtn = document.getElementById('convert-excel-btn');
const excelFileInput = document.getElementById('excel-file');
const resultBox = document.getElementById("result-box");

async function convertExcelFile() {
    
    let excelFile = excelFileInput.files[0];

    try {
        const response = exerciseApi.importExcel(excelFile);

        console.log(response);
        
    } catch(error) {
        console.error(error.message);
    }
}

function initEventListeners() {
    if (convertBtn) {
        convertBtn.addEventListener('click', () => convertExcelFile());
    }
}

function initExercisesImportPage() {
    initEventListeners();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initExercisesImportPage);
} else {
    initExercisesImportPage();
}