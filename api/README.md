# ⚠️ Legacy API (Deprecated)

> Bu klasör yalnızca geçmiş sürümler içindir. Canlı ve geliştirme ortamında **/server/api** kullanılmalıdır. Yeni .env dosyalarınızda `PUBLIC_API_URL=https://dijitalmentor.de/server/api` olmalı.

# Database Integration - README

## Setup Instructions

### 1. Database Setup

#### Option A: Fresh Installation
If you haven't set up the database yet:
```bash
# Upload install-hostinger.sql to your Hostinger phpMyAdmin
# Or run via SSH:
mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/install-hostinger.sql
```

#### Option B: Migrate Existing Database
If you already have a database running:
```bash
# Run the migration script:
mysql -u u553245641_dijitalmentor -p u553245641_dijitalmentor < database/migrate-approval-status.sql
```

### 2. Configure Database Connection

Edit `api/config/database.php` and update:
- `DB_PASS`: Your Hostinger database password
- `JWT_SECRET`: Change to a secure random string

### 3. Create Uploads Directory

```bash
mkdir -p uploads/avatars
chmod 755 uploads
chmod 755 uploads/avatars
```

### 4. Deploy API Files

Upload the `api/` folder to your Hostinger public_html:
```
public_html/
├── api/
│   ├── config/
│   ├── auth/
│   ├── teachers/
│   ├── profile/
│   └── subjects/
└── uploads/
```

### 5. Update Environment Variables

In your `.env` file (or GitHub Actions secrets), point to the maintained API:
```
PUBLIC_API_URL=https://dijitalmentor.de/server/api
PUBLIC_MOCK_MODE=false
```

### 6. Test API

Test endpoints:
- https://dijitalmentor.de/api/subjects/list.php
- https://dijitalmentor.de/api/teachers/list.php

## API Endpoints

### Authentication
- `POST /api/auth/login.php` - Login
- `POST /api/auth/register.php` - Register

### Teachers
- `GET /api/teachers/list.php` - List teachers (with filters)
- `GET /api/teachers/detail.php?id={id}` - Teacher details

### Profile
- `POST /api/profile/update.php` - Update profile
- `POST /api/profile/upload_avatar.php` - Upload avatar

### Subjects
- `GET /api/subjects/list.php` - List all subjects

## Development vs Production

### Local Development
Set in `.env`:
```
PUBLIC_MOCK_MODE=true
```

### Production
Set in `.env`:
```
PUBLIC_MOCK_MODE=false
PUBLIC_API_URL=https://dijitalmentor.de/server/api
```

## Security Notes

1. **Change JWT_SECRET** in `api/config/database.php`
2. **Update DB_PASS** with your actual password
3. **Never commit** sensitive credentials to Git
4. **Use HTTPS** in production (already configured on Hostinger)

## Troubleshooting

### "Database connection failed"
- Check database credentials in `api/config/database.php`
- Verify database exists in Hostinger cPanel

### "Upload failed"
- Check `uploads/` directory permissions (755)
- Verify PHP upload limits in `.htaccess`

### CORS errors
- Verify `.htaccess` is uploaded to `api/` folder
- Check browser console for specific error

## Next Steps

1. Upload database schema
2. Configure database connection
3. Deploy API files
4. Test with Postman or browser
5. Switch frontend to production mode
