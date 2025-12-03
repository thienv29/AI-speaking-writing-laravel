# Demo nhúng iframe bài tập I-CLC

Thư mục này chứa ví dụ tối giản để nhúng trang làm bài vào website khác thông qua `<iframe>` được hiển thị trong modal.

## Cách chạy nhanh

1. Cài `serve` (hoặc dùng bất kỳ static server nào):
   ```bash
   npm install -g serve
   ```
2. Chạy server ở port riêng (ví dụ 5174):
   ```bash
   serve embed-demo -l 5174
   ```
3. Truy cập `http://localhost:5174` và nhấn **Tải** hoặc **Popup**.

> Lưu ý: Ứng dụng Laravel chính (chạy qua Docker) phải đang chạy tại `http://localhost:8000` để iframe load được nội dung.

## Cách sử dụng

- **Exercise ID**: ID của exercise (bắt buộc)
- **Question ID**: ID của câu hỏi cụ thể (tùy chọn, nếu không có sẽ hiển thị câu hỏi đầu tiên)

## Routes

Trang demo sử dụng route mới:
- Writing: `/embed-writing/exercises/{exercise_id}?questionId={question_id}`
- Speaking: `/embed-speaking/exercises/{exercise_id}?questionId={question_id}`

Mặc định sử dụng route Writing. Để đổi sang Speaking, sửa trong `modal.js`:
- Đổi `embed-writing` thành `embed-speaking` trong hàm `buildUrl()`

## Tùy chỉnh
- Thay đổi URL embed trong `modal.js` nếu cần (biến `BASE_URL`).
- Đổi route từ `embed-writing` sang `embed-speaking` nếu cần.
