<<<<<<< HEAD
# Real-Estate
Enhanced Real Estate platform
=======
# RealtyFlow Pro

A comprehensive real estate marketing platform that connects property developers, agents, influencers, and clients through an integrated ecosystem.

## 🏗️ Project Overview

RealtyFlow Pro is a full-stack web application that enables real estate professionals to leverage influencer marketing for property promotion. The platform supports multiple user types with role-based access control and provides tools for campaign management, lead tracking, and analytics.

## ✨ Features

### 🔐 Multi-User Authentication System
- **Admin**: System administration, user management, analytics
- **Agent**: Property listing, lead management, commission tracking
- **Builder**: Project showcase, campaign management
- **Client**: Property search, favorites, inquiry management
- **Influencer**: Content creation, campaign participation, earnings tracking

### 🌍 Multi-Country Support
- Country-based marketplace selection
- Localized user experience
- Country-specific property listings

### 📊 Dashboard & Analytics
- Role-specific dashboards
- Real-time analytics and reporting
- Lead tracking and conversion metrics
- Commission calculation for agents

### 🎯 Campaign Management
- Influencer campaign creation and management
- Social media content scheduling
- Performance tracking and ROI analysis

### 🏠 Property Management
- Property listing and categorization
- Image gallery and virtual tours
- Status tracking (available, sold, pending)

### 📱 Social Media Integration
- Multi-platform social media management
- Content calendar and scheduling
- Performance analytics across platforms

## 🛠️ Tech Stack

### Frontend
- **React 18** - Modern UI framework
- **TypeScript** - Type-safe development
- **Vite** - Fast build tool and dev server
- **Tailwind CSS** - Utility-first CSS framework
- **React Router v6** - Client-side routing
- **Lucide Icons** - Beautiful icon library

### Backend
- **PHP 8.0+** - Server-side logic
- **MySQL/MariaDB** - Database
- **PDO** - Database abstraction layer
- **Apache/Nginx** - Web server

### Development Tools
- **Node.js** - Package management
- **npm** - Dependency management
- **PostCSS** - CSS processing
- **TypeScript Compiler** - Type checking

## 📁 Project Structure

```
Real-Estate/
├── api/                    # PHP API endpoints
│   ├── admin/             # Admin-specific APIs
│   ├── agent/             # Agent dashboard APIs
│   ├── auth/              # Authentication APIs
│   └── countries/         # Country management APIs
├── app/                   # PHP application core
│   ├── config/           # Configuration files
│   ├── core/             # Core classes and interfaces
│   ├── models/           # Data models
│   └── services/         # Business logic services
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   └── flags/           # Country flag images
├── components/           # PHP components (legacy)
├── config/              # Database configuration
├── database/            # Database migrations and seeds
├── includes/            # PHP includes and initialization
├── src/                 # React frontend source
│   ├── components/      # Reusable React components
│   ├── contexts/        # React contexts
│   ├── lib/            # Utility functions
│   ├── pages/          # Page components
│   └── services/       # API service functions
├── index.html          # Main HTML file
├── package.json        # Node.js dependencies
├── tailwind.config.js  # Tailwind configuration
├── tsconfig.json       # TypeScript configuration
└── vite.config.ts      # Vite configuration
```

## 🚀 Getting Started

### Prerequisites
- **Node.js** (v16 or higher)
- **PHP** (8.0 or higher)
- **MySQL/MariaDB** (5.7 or higher)
- **Apache/Nginx** web server
- **Composer** (for PHP dependencies)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Real-Estate
   ```

2. **Install frontend dependencies**
   ```bash
   npm install
   ```

3. **Configure database**
   - Create a MySQL database named `real_estate_local`
   - Update `config/db.config.local.php` with your database credentials:
   ```php
   <?php
   return [
       'host' => 'localhost',
       'port' => 3306,
       'username' => 'root',
       'password' => '',
       'database' => 'real_estate_local'
   ];
   ```

4. **Import database schema**
   ```bash
   mysql -u root -p real_estate_local < database/f63845733780033.sql
   ```

5. **Start the development server**
   ```bash
   npm run dev
   ```

6. **Access the application**
   - Frontend: http://localhost:5173
   - Backend API: http://localhost/Real-Estate/api/

### Production Deployment

#### GoDaddy Hosting Setup for bilionsales.com/Real-Estate

1. **Upload files to GoDaddy**
   - Upload the entire project to `/home/youruser/public_html/Real-Estate`
   - Ensure the path is accessible at `https://bilionsales.com/Real-Estate`

2. **Database setup**
   - Create a new MySQL database in GoDaddy cPanel
   - Import the database schema
   - Update `config/db.config.php` with production credentials

3. **Build frontend for production**
   ```bash
   npm run build
   ```
   - Upload the contents of the `dist` folder to your hosting directory

4. **Configure .htaccess** (if needed)
   ```apache
   RewriteEngine On
   RewriteBase /Real-Estate/
   RewriteRule ^index\.html$ - [L]
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule . /Real-Estate/index.html [L]
   ```

#### Production Project Structure for bilionsales.com/Real-Estate

When deploying to production, your project structure should look like this:

```
public_html/Real-Estate/
├── api/                    # PHP API endpoints (KEEP)
│   ├── admin/
│   ├── agent/
│   ├── auth/
│   └── countries/
├── app/                   # PHP application core (KEEP)
│   ├── config/
│   ├── core/
│   ├── models/
│   └── services/
├── assets/               # Static assets (KEEP)
│   ├── css/
│   └── flags/
├── config/              # Database configuration (KEEP)
├── database/            # Database files (KEEP)
├── includes/            # PHP includes (KEEP)
├── dist/                # Built React app (UPLOAD CONTENTS)
│   ├── index.html
│   ├── assets/
│   └── ...
├── .htaccess            # Apache configuration (CREATE)
├── index.html           # Main entry point (FROM dist/)
└── README.md            # Documentation (OPTIONAL)
```

#### Files to Upload for Production

**✅ KEEP (Essential for Production):**
- `api/` - All PHP API endpoints
- `app/` - PHP application core
- `assets/` - Static assets (CSS, images, flags)
- `config/` - Database configuration
- `database/` - Database schema and migrations
- `includes/` - PHP initialization files
- `dist/` - Built React application (upload contents to root)

**❌ EXCLUDE (Development Only):**
- `src/` - React source code (not needed in production)
- `node_modules/` - Node.js dependencies
- `package.json` - Development dependencies
- `package-lock.json` - Development lock file
- `tailwind.config.js` - Development configuration
- `tsconfig.json` - TypeScript configuration
- `vite.config.ts` - Vite configuration
- `.git/` - Version control
- `.gitignore` - Git ignore file
- `postcss.config.js` - Development PostCSS config

#### Step-by-Step Production Deployment

1. **Build the React Application**
   ```bash
   npm run build
   ```

2. **Prepare Production Files**
   ```bash
   # Create production directory
   mkdir production
   
   # Copy essential PHP files
   cp -r api/ production/
   cp -r app/ production/
   cp -r assets/ production/
   cp -r config/ production/
   cp -r database/ production/
   cp -r includes/ production/
   
   # Copy built React app
   cp -r dist/* production/
   
   # Create .htaccess for React Router
   echo "RewriteEngine On
   RewriteBase /Real-Estate/
   RewriteRule ^index\.html$ - [L]
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule . /Real-Estate/index.html [L]" > production/.htaccess
   ```

3. **Upload to GoDaddy**
   - Upload the entire `production/` folder contents to `/home/youruser/public_html/Real-Estate/`
   - Ensure all files are in the correct location

4. **Configure Database**
   - Update `config/db.config.php` with production database credentials:
   ```php
   <?php
   return [
       'host' => 'your-godaddy-mysql-host',
       'port' => 3306,
       'username' => 'your-database-username',
       'password' => 'your-database-password',
       'database' => 'your-database-name'
   ];
   ```

5. **Import Database Schema**
   - Use phpMyAdmin in GoDaddy cPanel
   - Import `database/f63845733780033.sql`

6. **Test the Application**
   - Visit `https://bilionsales.com/Real-Estate`
   - Test all user roles and functionality
   - Verify API endpoints work correctly

#### Production Configuration Checklist

- [ ] Database credentials updated in `config/db.config.php`
- [ ] React app built with `npm run build`
- [ ] All PHP files uploaded to correct location
- [ ] Built React files uploaded to root directory
- [ ] `.htaccess` file created for React Router
- [ ] Database schema imported
- [ ] Country flags uploaded to `assets/flags/`
- [ ] CORS headers configured for production domain
- [ ] Error reporting disabled in PHP
- [ ] File permissions set correctly (644 for files, 755 for directories)

#### Production URLs

- **Main Application**: `https://bilionsales.com/Real-Estate`
- **API Endpoints**: `https://bilionsales.com/Real-Estate/api/`
- **Static Assets**: `https://bilionsales.com/Real-Estate/assets/`
- **Country Flags**: `https://bilionsales.com/Real-Estate/assets/flags/`

#### Troubleshooting Production Issues

1. **404 Errors**: Check `.htaccess` configuration
2. **Database Connection**: Verify credentials in `config/db.config.php`
3. **CORS Issues**: Update CORS headers in PHP files for production domain
4. **Missing Assets**: Ensure all files uploaded to correct paths
5. **React Router Issues**: Verify `.htaccess` rewrite rules

#### Security Considerations for Production

- Remove development files (`src/`, `node_modules/`, etc.)
- Disable PHP error reporting in production
- Use HTTPS for all API calls
- Implement proper CORS policies
- Regular database backups
- Monitor error logs

## 🔧 Configuration

### Environment Variables
- Database connection settings in `config/db.config.php`
- API endpoints configuration
- CORS settings for development

### API Endpoints

#### Authentication
- `POST /api/auth/login.php` - User login
- `POST /api/auth/register.php` - User registration

#### Admin
- `GET /api/admin/users.php` - List all users
- `POST /api/admin/users.php` - Create new user
- `DELETE /api/admin/users.php` - Delete user
- `GET /api/admin/dashboard.php` - Admin dashboard data

#### Countries
- `GET /api/countries/list.php` - List all countries

## 👥 User Roles & Permissions

### Admin
- User management (create, delete, view all users)
- System analytics and reporting
- Platform configuration

### Agent
- Property listing and management
- Lead tracking and management
- Commission calculation

### Builder
- Project showcase and management
- Campaign creation and management
- Property portfolio management

### Client
- Property search and browsing
- Favorites and inquiry management
- Account management

### Influencer
- Content creation and management
- Campaign participation
- Earnings tracking and analytics

## 🎨 UI Components

The application uses a comprehensive component library:
- **GradientButton** - Styled buttons with variants
- **Card** - Content containers
- **Modal** - Overlay dialogs
- **DashboardLayout** - Role-specific dashboard layouts
- **CountrySelector** - Country selection interface

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- CORS configuration for API security
- Role-based access control
- Input validation and sanitization
- SQL injection prevention using PDO prepared statements

## 📊 Database Schema

### Core Tables
- `users` - User accounts and profiles
- `countries` - Country information
- `properties` - Property listings
- `leads` - Lead management
- `campaigns` - Marketing campaigns

### Key Relationships
- Users are associated with countries via `country_id`
- Properties are linked to builders/agents
- Leads are connected to properties and users

## 🚀 Available Scripts

```bash
npm run dev          # Start development server
npm run build        # Build for production
npm run preview      # Preview production build
npm run lint         # Run ESLint
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🔄 Version History

- **v1.0.0** - Initial release with core functionality
- Multi-user authentication system
- Dashboard implementations
- API endpoints
- Country-based marketplace
- User management system

---

**Built with ❤️ for the real estate industry**
>>>>>>> main
