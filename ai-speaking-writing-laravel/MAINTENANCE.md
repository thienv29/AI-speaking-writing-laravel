# Hướng dẫn Bảo trì và Nâng cấp

## 🔧 Các tác vụ bảo trì thường xuyên

### 1. Xem logs
```bash
# Xem logs của app
docker-compose logs -f app

# Xem logs của MySQL
docker-compose logs -f mysql

# Xem logs của tất cả services
docker-compose logs -f
```

### 2. Restart services
```bash
# Restart tất cả services
docker-compose restart

# Restart chỉ app
docker-compose restart app

# Restart chỉ MySQL
docker-compose restart mysql
```

### 3. Clear cache
```bash
# Clear tất cả cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Rebuild cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 4. Database operations

#### Backup database
```bash
docker-compose exec mysql mysqldump -u iclc_user -ppassword iclc_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Restore database
```bash
docker-compose exec -T mysql mysql -u iclc_user -ppassword iclc_db < backup_file.sql
```

#### Chạy migrations mới
```bash
docker-compose exec app php artisan migrate
```

#### Reset database (XÓA HẾT DỮ LIỆU)
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### 5. Update dependencies

#### Update Composer packages
```bash
docker-compose exec app composer update
docker-compose exec app composer install --optimize-autoloader
```

#### Rebuild containers
```bash
docker-compose build --no-cache
docker-compose up -d
```

## 🚀 Nâng cấp hệ thống

### 1. Update code từ git
```bash
git pull
docker-compose up -d --build
```

### 2. Update với zero downtime
```bash
# Pull code mới
git pull

# Build image mới (không stop container cũ)
docker-compose build

# Restart với image mới
docker-compose up -d

# Hoặc rolling update
docker-compose up -d --no-deps --build app
```

### 3. Rollback về version cũ
```bash
git checkout <commit-hash>
docker-compose up -d --build
```

## 🔍 Monitoring và Debugging

### 1. Check container status
```bash
docker-compose ps
```

### 2. Check resource usage
```bash
docker stats
```

### 3. Access container shell
```bash
# Access app container
docker-compose exec app bash

# Access MySQL container
docker-compose exec mysql bash

# Run Laravel tinker
docker-compose exec app php artisan tinker
```

### 4. Check database
```bash
docker-compose exec mysql mysql -u iclc_user -ppassword iclc_db
```

## 🛠️ Troubleshooting

### Container không start
```bash
# Xem logs lỗi
docker-compose logs app

# Check container status
docker-compose ps

# Restart từ đầu
docker-compose down
docker-compose up -d
```

### Database connection error
```bash
# Check MySQL status
docker-compose logs mysql

# Test connection
docker-compose exec app php artisan tinker
# Trong tinker: DB::connection()->getPdo();
```

### Migration errors
```bash
# Check migration status
docker-compose exec app php artisan migrate:status

# Rollback migration
docker-compose exec app php artisan migrate:rollback

# Force refresh
docker-compose exec app php artisan migrate:fresh --seed
```

### Permission issues
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## 📊 Performance Optimization

### 1. Enable OPcache (đã có trong Dockerfile)
OPcache đã được enable và config trong Dockerfile.

### 2. Optimize Laravel
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 3. Clear old logs
```bash
# Xóa logs cũ hơn 7 ngày
find storage/logs -name "*.log" -mtime +7 -delete
```

## 🔐 Security

### 1. Update dependencies
```bash
docker-compose exec app composer update
```

### 2. Check for vulnerabilities
```bash
docker-compose exec app composer audit
```

### 3. Review environment variables
Đảm bảo `.env` không chứa thông tin nhạy cảm và không commit vào git.

## 📝 Best Practices

1. **Backup thường xuyên**: Chạy backup database định kỳ
2. **Monitor logs**: Kiểm tra logs thường xuyên để phát hiện lỗi sớm
3. **Update dependencies**: Cập nhật Composer packages định kỳ
4. **Test trước khi deploy**: Test trên môi trường dev trước khi deploy production
5. **Document changes**: Ghi lại các thay đổi quan trọng

