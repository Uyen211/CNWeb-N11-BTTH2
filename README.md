# Dự Án Website Khóa Học Trực Tuyến (OnlineCourse)

## 1. Giới thiệu
Hệ thống quản lý khóa học trực tuyến xây dựng theo mô hình MVC PHP thuần.
- **Database:** MySQL (phpMyAdmin)
- **Tên CSDL:** CSDLonlinecourse

## 2. Yêu cầu hệ thống
- XAMPP / WAMP / Laragon (PHP >= 7.4)
- MySQL Database

## 3. Cài đặt & Chạy dự án
1. **Clone dự án:** Tải thư mục `onlinecourse` vào `htdocs` (nếu dùng XAMPP).
2. **Cấu hình Database:**
   - Mở phpMyAdmin, tạo database mới tên: `CSDLonlinecourse`
   - Import file SQL cấu trúc bảng (file .sql đã thống nhất).
   - Kiểm tra file `config/Database.php` để đảm bảo user/pass đúng (mặc định user: `root`, pass: rỗng).
3. **Chạy:** Mở trình duyệt truy cập `http://localhost/onlinecourse`

## 4. Quy tắc Code (Convention)
- **Tên Class:** Viết Hoa Chữ Cái Đầu (VD: `CourseController`)
- **Tên biến:** camelCase (VD: `userName`, `courseId`)

## 5. Quy tắc Commit (Git Convention)

Mọi commit cần tuân thủ cấu trúc:  
`type(scope): subject`

**1. Các loại (Type) bắt buộc:**
- `feat`: Thêm tính năng mới (Feature)
- `fix`: Sửa lỗi (Bug fix)
- `docs`: Viết/sửa tài liệu (Documentation)
- `style`: Sửa lỗi trình bày, format code (Không sửa logic)
- `refactor`: Tối ưu/Sắp xếp lại code (Không thêm tính năng mới)
- `chore`: Việc vặt (Cấu hình, .gitignore, database...)

**2. Ví dụ mẫu:**
- `feat(auth): add login function`
- `fix(home): fix slider image responsiveness`
- `docs: update readme instruction`
