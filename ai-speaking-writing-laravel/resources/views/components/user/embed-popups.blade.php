<!-- Loading Popup -->
<div class="popup-overlay" id="loadingPopup">
    <div class="popup-content loading-popup-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">Đang chấm bài...</div>
    </div>
</div>

<!-- Result Popup -->
<div class="popup-overlay" id="resultPopup">
    <div class="popup-content result-popup-content">
        <button class="popup-close" id="resultPopupClose">×</button>
        <div id="resultPopupContent"></div>
    </div>
</div>

<!-- Instruction Popup -->
@if(!empty($exercise->instruction))
    <div class="popup-overlay" id="instructionPopup">
        <div class="popup-content instruction-popup-content">
            <button class="popup-close" id="instructionPopupClose">×</button>
            <div class="instruction-popup-header">
                <span class="instruction-icon-large">📝</span>
                <h3>Hướng dẫn</h3>
            </div>
            <div class="instruction-popup-body">
                <div class="instruction-text" id="instructionText">{{ $exercise->instruction ?? '' }}</div>
            </div>
        </div>
    </div>
@endif

<!-- Statistics Popup -->
<div class="popup-overlay" id="statisticsPopup">
    <div class="popup-content statistics-popup-content">
        <button class="popup-close" id="statisticsPopupClose">×</button>
        <div id="statisticsPopupContent"></div>
    </div>
</div>

