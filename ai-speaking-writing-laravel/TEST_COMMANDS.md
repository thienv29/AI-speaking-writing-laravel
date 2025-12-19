# Hướng dẫn Test Docker trên Local

## 1. Khởi động services

```bash
# Build và start tất cả services
docker compose up -d --build

# Hoặc chỉ start (nếu đã build rồi)
docker compose up -d

# Xem logs real-time
docker compose logs -f app
```

## 2. Kiểm tra services đang chạy

```bash
# Xem trạng thái tất cả containers
docker compose ps

# Kiểm tra health status
docker compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"
```

## 3. Test Admin Users CRUD

### A. Truy cập qua browser:
```
http://localhost:8000/admin/users
```

### B. Test qua command line:

```bash
# Test route admin/users
docker compose exec app php artisan route:list --name=admin.users

# Test xem có users không
docker compose exec app php artisan tinker --execute="echo 'Users: ' . \App\Models\User::count();"

# Test controller trực tiếp
docker compose exec app php artisan tinker --execute="
\$controller = new \App\Http\Controllers\Admin\UserController();
\$request = new \Illuminate\Http\Request();
\$users = \$controller->index(\$request);
echo 'Users page loaded successfully';
"
```

## 4. Kiểm tra logs

```bash
# Xem logs của app service
docker compose logs app

# Xem logs real-time
docker compose logs -f app

# Xem logs của tất cả services
docker compose logs

# Xem logs của MySQL
docker compose logs mysql

# Xem logs của ai-speech-service
docker compose logs ai-speech-service
```

## 5. Vào container để test

```bash
# Vào container app
docker compose exec app bash

# Trong container, bạn có thể chạy:
php artisan route:list
php artisan tinker
php artisan migrate:status
php artisan db:seed --class=DatabaseSeeder
```

## 6. Test database

```bash
# Vào MySQL container
docker compose exec mysql mysql -u iclc_user -p iclc_db

# Hoặc dùng root
docker compose exec mysql mysql -u root -p

# Test query users
docker compose exec mysql mysql -u iclc_user -p'password' iclc_db -e "SELECT id, name, email, role FROM users;"
```

## 7. Test API endpoints

```bash
# Test admin users index
curl http://localhost:8000/admin/users

# Test với authentication (nếu có)
curl -H "Accept: application/json" http://localhost:8000/admin/users

# Test API users
curl http://localhost:8000/api/users
```

## 8. Restart services

```bash
# Restart app service
docker compose restart app

# Restart tất cả
docker compose restart

# Stop tất cả
docker compose stop

# Start lại
docker compose start

# Down và up lại (xóa containers, giữ volumes)
docker compose down
docker compose up -d
```

## 9. Rebuild và test lại

```bash
# Rebuild app service
docker compose build app

# Rebuild và restart
docker compose up -d --build app

# Rebuild tất cả
docker compose build --no-cache
docker compose up -d
```

## 10. Kiểm tra file trong container

```bash
# Kiểm tra view files
docker compose exec app ls -la resources/views/admin/users/

# Kiểm tra controller
docker compose exec app cat app/Http/Controllers/Admin/UserController.php | head -50

# Kiểm tra routes
docker compose exec app cat routes/admin.php | grep users
```

## 11. Test với fresh database

```bash
# Xóa volumes và rebuild
docker compose down -v
docker compose up -d --build

# Hoặc chỉ reset database
docker compose exec app php artisan migrate:fresh --seed
```

## 12. Kiểm tra network và ports

```bash
# Xem ports đang expose
docker compose ps

# Test connection
curl http://localhost:8000/admin
curl http://localhost:8000/admin/users
curl http://localhost:8000/api/users
```

## 13. Debug issues

```bash
# Xem logs lỗi
docker compose logs app | grep -i error

# Xem logs Laravel
docker compose exec app tail -f storage/logs/laravel.log

# Kiểm tra permissions
docker compose exec app ls -la storage/
docker compose exec app ls -la bootstrap/cache/
```

## 14. Test cụ thể Admin Users

```bash
# 1. Kiểm tra routes
docker compose exec app php artisan route:list --name=admin.users

# 2. Test index page
curl -s http://localhost:8000/admin/users | grep -i "Quản lý Người dùng"

# 3. Test create page
curl -s http://localhost:8000/admin/users/create | grep -i "Tạo người dùng"

# 4. Test với user ID cụ thể
curl -s http://localhost:8000/admin/users/1 | grep -i "Chi tiết Người dùng"

# 5. Kiểm tra database có users không
docker compose exec app php artisan tinker --execute="
\$users = \App\Models\User::all(['id', 'name', 'email', 'role']);
foreach(\$users as \$u) {
    echo \"ID: {\$u->id}, Name: {\$u->name}, Email: {\$u->email}, Role: {\$u->role}\" . PHP_EOL;
}
"
```

## 15. Quick test script

Tạo file `test-admin-users.sh`:

```bash
#!/bin/bash
echo "=== Testing Admin Users ==="
echo "1. Checking routes..."
docker compose exec app php artisan route:list --name=admin.users

echo -e "\n2. Checking users in database..."
docker compose exec app php artisan tinker --execute="echo 'Total users: ' . \App\Models\User::count();"

echo -e "\n3. Testing index page..."
curl -s http://localhost:8000/admin/users | grep -q "Quản lý Người dùng" && echo "✓ Index page OK" || echo "✗ Index page FAILED"

echo -e "\n4. Testing create page..."
curl -s http://localhost:8000/admin/users/create | grep -q "Tạo người dùng" && echo "✓ Create page OK" || echo "✗ Create page FAILED"

echo -e "\n=== Test Complete ==="
```

Chạy: `chmod +x test-admin-users.sh && ./test-admin-users.sh`

