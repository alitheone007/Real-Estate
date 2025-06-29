-- Enhanced RealtyFlow Pro Database Schema
-- Includes all new features: Analytics, Timezone Management, Feedback System, User Activity, PWA

-- =====================================================
-- ENHANCED CORE TABLES
-- =====================================================

-- Enhanced users table with new columns
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_active TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS deactivated_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS timezone VARCHAR(50);
ALTER TABLE users ADD COLUMN IF NOT EXISTS notification_preferences JSON;
ALTER TABLE users ADD COLUMN IF NOT EXISTS whatsapp_number VARCHAR(20);
ALTER TABLE users ADD COLUMN IF NOT EXISTS whatsapp_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_links JSON;

-- Enhanced countries table with timezone support
ALTER TABLE countries ADD COLUMN IF NOT EXISTS timezone VARCHAR(50);
ALTER TABLE countries ADD COLUMN IF NOT EXISTS operational_hours JSON;
ALTER TABLE countries ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;
ALTER TABLE countries ADD COLUMN IF NOT EXISTS currency_code VARCHAR(3);
ALTER TABLE countries ADD COLUMN IF NOT EXISTS currency_symbol VARCHAR(5);
ALTER TABLE countries ADD COLUMN IF NOT EXISTS phone_code VARCHAR(10);

-- Enhanced properties table with analytics
ALTER TABLE properties ADD COLUMN IF NOT EXISTS views_count INT DEFAULT 0;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS last_viewed TIMESTAMP NULL;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS seo_title VARCHAR(255);
ALTER TABLE properties ADD COLUMN IF NOT EXISTS seo_description TEXT;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS featured BOOLEAN DEFAULT FALSE;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS featured_until TIMESTAMP NULL;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS property_images JSON;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS amenities JSON;
ALTER TABLE properties ADD COLUMN IF NOT EXISTS virtual_tour_url VARCHAR(255);

-- =====================================================
-- ANALYTICS & IP TRACKING SYSTEM
-- =====================================================

-- User activity tracking
CREATE TABLE user_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL for anonymous users
    ip_address VARCHAR(45),
    country_code VARCHAR(10),
    city VARCHAR(100),
    region VARCHAR(100),
    timezone VARCHAR(50),
    user_agent TEXT,
    page_visited VARCHAR(255),
    session_duration INT DEFAULT 0, -- in seconds
    click_count INT DEFAULT 1,
    referrer VARCHAR(255),
    device_type ENUM('desktop', 'mobile', 'tablet') DEFAULT 'desktop',
    browser VARCHAR(100),
    os VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- IP geolocation cache
CREATE TABLE ip_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE,
    country_code VARCHAR(10),
    country_name VARCHAR(100),
    city VARCHAR(100),
    region VARCHAR(100),
    timezone VARCHAR(50),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    isp VARCHAR(255),
    cached_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Session tracking
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(255) UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =====================================================
-- TIMEZONE & MARKETPLACE OPERATIONAL HOURS
-- =====================================================

-- Country operational hours
CREATE TABLE country_operational_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT,
    timezone VARCHAR(50),
    operational_start TIME DEFAULT '10:00:00',
    operational_end TIME DEFAULT '20:00:00',
    is_operational BOOLEAN DEFAULT TRUE,
    weekend_operational BOOLEAN DEFAULT FALSE,
    holiday_operational BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id)
);

-- Marketplace status cache
CREATE TABLE marketplace_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT,
    current_status ENUM('operational', 'non-operational', 'limited') DEFAULT 'operational',
    current_time_local TIME,
    next_operational_time TIMESTAMP,
    status_message VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id)
);

-- =====================================================
-- FEEDBACK & ISSUE MANAGEMENT SYSTEM
-- =====================================================

-- Feedback tickets
CREATE TABLE feedback_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL for anonymous
    name VARCHAR(100),
    email VARCHAR(150),
    subject VARCHAR(255),
    message TEXT,
    category ENUM('bug', 'feature_request', 'general', 'support', 'complaint') DEFAULT 'general',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT NULL,
    resolution_notes TEXT,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Ticket responses
CREATE TABLE ticket_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT,
    user_id INT NULL, -- NULL for admin responses
    message TEXT,
    is_internal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES feedback_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Ticket attachments
CREATE TABLE ticket_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT,
    response_id INT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    mime_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES feedback_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (response_id) REFERENCES ticket_responses(id) ON DELETE CASCADE
);

-- =====================================================
-- USER ACTIVITY & INACTIVITY MANAGEMENT
-- =====================================================

-- User activity tracking
CREATE TABLE user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    last_login TIMESTAMP,
    last_active TIMESTAMP,
    session_duration INT DEFAULT 0, -- in seconds
    pages_visited INT DEFAULT 0,
    actions_performed INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Inactivity notifications
CREATE TABLE inactivity_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    notification_type ENUM('30_days', '90_days', '180_days'),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    action_taken BOOLEAN DEFAULT FALSE,
    notification_method ENUM('email', 'whatsapp', 'both') DEFAULT 'email',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- WhatsApp integration
CREATE TABLE whatsapp_integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    phone_number VARCHAR(20),
    whatsapp_id VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE,
    last_sync TIMESTAMP,
    opt_in_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- PWA & NOTIFICATION SYSTEM
-- =====================================================

-- Push notification subscriptions
CREATE TABLE push_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    endpoint VARCHAR(500),
    p256dh_key VARCHAR(255),
    auth_token VARCHAR(255),
    device_info JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error', 'promotion') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(500),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- ENHANCED PROPERTY MANAGEMENT
-- =====================================================

-- Property views tracking
CREATE TABLE property_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT,
    user_id INT NULL,
    ip_address VARCHAR(45),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_duration INT DEFAULT 0,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Property favorites
CREATE TABLE property_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    property_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, property_id)
);

-- =====================================================
-- MOCK DATA INSERTION
-- =====================================================

-- Update existing countries with timezone data
UPDATE countries SET 
    timezone = 'America/New_York',
    currency_code = 'USD',
    currency_symbol = '$',
    phone_code = '+1'
WHERE code = 'US';

UPDATE countries SET 
    timezone = 'America/Toronto',
    currency_code = 'CAD',
    currency_symbol = 'C$',
    phone_code = '+1'
WHERE code = 'CA';

UPDATE countries SET 
    timezone = 'Europe/London',
    currency_code = 'GBP',
    currency_symbol = '£',
    phone_code = '+44'
WHERE code = 'GB';

UPDATE countries SET 
    timezone = 'Australia/Sydney',
    currency_code = 'AUD',
    currency_symbol = 'A$',
    phone_code = '+61'
WHERE code = 'AU';

UPDATE countries SET 
    timezone = 'Europe/Berlin',
    currency_code = 'EUR',
    currency_symbol = '€',
    phone_code = '+49'
WHERE code = 'DE';

UPDATE countries SET 
    timezone = 'Asia/Shanghai',
    currency_code = 'CNY',
    currency_symbol = '¥',
    phone_code = '+86'
WHERE code = 'CN';

UPDATE countries SET 
    timezone = 'Asia/Kolkata',
    currency_code = 'INR',
    currency_symbol = '₹',
    phone_code = '+91'
WHERE code = 'IN';

UPDATE countries SET 
    timezone = 'Asia/Dubai',
    currency_code = 'AED',
    currency_symbol = 'د.إ',
    phone_code = '+971'
WHERE code = 'AE';

-- Insert operational hours for countries
INSERT INTO country_operational_hours (country_id, timezone, operational_start, operational_end, is_operational, weekend_operational) VALUES
(1, 'America/New_York', '09:00:00', '18:00:00', TRUE, FALSE),
(2, 'America/Toronto', '09:00:00', '18:00:00', TRUE, FALSE),
(3, 'Europe/London', '09:00:00', '17:30:00', TRUE, FALSE),
(4, 'Australia/Sydney', '08:30:00', '17:30:00', TRUE, FALSE),
(5, 'Europe/Berlin', '08:00:00', '18:00:00', TRUE, FALSE),
(6, 'Asia/Shanghai', '09:00:00', '18:00:00', TRUE, FALSE),
(7, 'Asia/Kolkata', '10:00:00', '19:00:00', TRUE, FALSE),
(8, 'Asia/Dubai', '08:00:00', '17:00:00', TRUE, FALSE);

-- Insert sample marketplace status
INSERT INTO marketplace_status (country_id, current_status, current_time_local, next_operational_time, status_message) VALUES
(1, 'operational', '14:30:00', '2024-01-15 09:00:00', 'Marketplace is currently operational'),
(2, 'operational', '14:30:00', '2024-01-15 09:00:00', 'Marketplace is currently operational'),
(3, 'operational', '19:30:00', '2024-01-16 09:00:00', 'Marketplace is currently operational'),
(4, 'non-operational', '03:30:00', '2024-01-15 08:30:00', 'Marketplace is currently closed. Opens at 8:30 AM'),
(5, 'operational', '20:30:00', '2024-01-16 08:00:00', 'Marketplace is currently operational'),
(6, 'operational', '02:30:00', '2024-01-15 09:00:00', 'Marketplace is currently operational'),
(7, 'operational', '00:00:00', '2024-01-15 10:00:00', 'Marketplace is currently operational'),
(8, 'operational', '22:30:00', '2024-01-15 08:00:00', 'Marketplace is currently operational');

-- Insert sample feedback tickets
INSERT INTO feedback_tickets (user_id, name, email, subject, message, category, priority, status) VALUES
(NULL, 'John Doe', 'john@example.com', 'Website loading slowly', 'The website takes too long to load on mobile devices', 'bug', 'medium', 'open'),
(4, 'Sarah Client', 'sarah@example.com', 'Feature request: Dark mode', 'Would love to have a dark mode option for better user experience', 'feature_request', 'low', 'open'),
(NULL, 'Anonymous User', 'user@example.com', 'Great platform!', 'Really enjoying using this platform for property search', 'general', 'low', 'resolved'),
(6, 'John Agent', 'agent@example.com', 'Need help with property upload', 'Having trouble uploading property images', 'support', 'high', 'in_progress');

-- Insert sample user activity
INSERT INTO user_activity (user_id, last_login, last_active, session_duration, pages_visited, actions_performed) VALUES
(1, '2024-01-14 10:00:00', '2024-01-14 15:30:00', 19800, 15, 25),
(3, '2024-01-14 09:15:00', '2024-01-14 12:45:00', 12600, 8, 12),
(4, '2024-01-14 11:30:00', '2024-01-14 16:20:00', 17400, 20, 30),
(5, '2024-01-14 08:45:00', '2024-01-14 14:15:00', 19800, 12, 18),
(6, '2024-01-14 13:20:00', '2024-01-14 17:45:00', 15900, 10, 15),
(7, '2024-01-14 10:30:00', '2024-01-14 13:50:00', 12000, 6, 8),
(8, '2024-01-14 14:00:00', '2024-01-14 18:30:00', 16200, 14, 22);

-- Insert sample analytics data
INSERT INTO user_analytics (user_id, ip_address, country_code, city, region, timezone, user_agent, page_visited, click_count, device_type, browser, os) VALUES
(1, '192.168.1.1', 'US', 'New York', 'New York', 'America/New_York', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '/admin', 25, 'desktop', 'Chrome', 'Windows'),
(4, '192.168.1.2', 'IN', 'Mumbai', 'Maharashtra', 'Asia/Kolkata', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', '/client', 30, 'mobile', 'Safari', 'iOS'),
(6, '192.168.1.3', 'CA', 'Toronto', 'Ontario', 'America/Toronto', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15)', '/agent', 18, 'desktop', 'Firefox', 'macOS'),
(NULL, '192.168.1.4', 'GB', 'London', 'England', 'Europe/London', 'Mozilla/5.0 (Android 10; Mobile)', '/', 5, 'mobile', 'Chrome', 'Android'),
(3, '192.168.1.5', 'AU', 'Sydney', 'New South Wales', 'Australia/Sydney', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '/builder', 22, 'desktop', 'Edge', 'Windows');

-- Insert sample property views
INSERT INTO property_views (property_id, user_id, ip_address, viewed_at, session_duration) VALUES
(1, 4, '192.168.1.2', '2024-01-14 10:30:00', 300),
(1, NULL, '192.168.1.6', '2024-01-14 11:15:00', 180),
(2, 4, '192.168.1.2', '2024-01-14 12:00:00', 450),
(3, 6, '192.168.1.3', '2024-01-14 13:20:00', 600),
(4, NULL, '192.168.1.7', '2024-01-14 14:45:00', 240);

-- Insert sample property favorites
INSERT INTO property_favorites (user_id, property_id) VALUES
(4, 1),
(4, 2),
(6, 3),
(7, 1);

-- Update properties with view counts
UPDATE properties SET views_count = 2 WHERE id = 1;
UPDATE properties SET views_count = 1 WHERE id = 2;
UPDATE properties SET views_count = 1 WHERE id = 3;
UPDATE properties SET views_count = 1 WHERE id = 4;

-- Insert sample notifications
INSERT INTO notifications (user_id, title, message, type, action_url) VALUES
(4, 'New Property Available', 'A new property matching your criteria is now available!', 'info', '/properties/5'),
(6, 'Lead Update', 'You have a new lead inquiry for your property', 'success', '/agent/leads'),
(3, 'Campaign Performance', 'Your latest campaign is performing well!', 'success', '/builder/campaigns'),
(5, 'Account Verification', 'Please verify your WhatsApp number for better communication', 'warning', '/influencer/settings');

-- Insert sample inactivity notifications
INSERT INTO inactivity_notifications (user_id, notification_type, notification_method) VALUES
(7, '30_days', 'email'),
(8, '30_days', 'both');

-- Insert sample WhatsApp integrations
INSERT INTO whatsapp_integrations (user_id, phone_number, is_verified) VALUES
(6, '+1234567890', TRUE),
(7, '+9198765432', FALSE),
(8, '+971501234567', TRUE);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Analytics indexes
CREATE INDEX idx_user_analytics_user_id ON user_analytics(user_id);
CREATE INDEX idx_user_analytics_created_at ON user_analytics(created_at);
CREATE INDEX idx_user_analytics_ip_address ON user_analytics(ip_address);

-- Activity indexes
CREATE INDEX idx_user_activity_user_id ON user_activity(user_id);
CREATE INDEX idx_user_activity_last_active ON user_activity(last_active);

-- Property views indexes
CREATE INDEX idx_property_views_property_id ON property_views(property_id);
CREATE INDEX idx_user_views_user_id ON property_views(user_id);
CREATE INDEX idx_property_views_viewed_at ON property_views(viewed_at);

-- Feedback indexes
CREATE INDEX idx_feedback_tickets_user_id ON feedback_tickets(user_id);
CREATE INDEX idx_feedback_tickets_status ON feedback_tickets(status);
CREATE INDEX idx_feedback_tickets_priority ON feedback_tickets(priority);

-- Notification indexes
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_is_read ON notifications(is_read);

-- Session indexes
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_user_sessions_session_id ON user_sessions(session_id); 

-- =====================================================
-- CAMPAIGN MANAGEMENT SYSTEM (2024-06-25)
-- =====================================================

-- Campaigns table
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed', 'custom') DEFAULT 'percentage',
    discount_value DECIMAL(10,2) DEFAULT 0.00,
    start_date DATE,
    end_date DATE,
    created_by INT,
    creator_role ENUM('influencer', 'builder', 'admin') DEFAULT 'influencer',
    status ENUM('active', 'inactive', 'expired', 'draft') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Campaign to property association
CREATE TABLE IF NOT EXISTS campaign_properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    property_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE KEY unique_campaign_property (campaign_id, property_id)
);

-- Campaign event tracking (clicks, conversions, etc.)
CREATE TABLE IF NOT EXISTS campaign_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    influencer_id INT NULL,
    builder_id INT NULL,
    user_id INT NULL,
    property_id INT NULL,
    event_type ENUM('click', 'conversion', 'view', 'share') DEFAULT 'click',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (influencer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (builder_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

-- END CAMPAIGN MANAGEMENT TABLES 