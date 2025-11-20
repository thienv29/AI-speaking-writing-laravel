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
3. Truy cập `http://localhost:5174` và nhấn **Mở bài tập**.

> Lưu ý: Ứng dụng Laravel chính (chạy qua Docker) phải đang chạy tại `http://localhost:8000` để iframe load được nội dung.

## Tùy chỉnh
- Thay đổi URL embed trong `modal.js` nếu cần (biến `BASE_URL`).
- Có thể thêm dropdown chọn lesson/question theo nhu cầu thực tế.
