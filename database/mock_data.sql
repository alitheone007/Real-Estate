-- Mock Data for RealtyFlow Pro
-- This file contains sample data for testing all features

-- =====================================================
-- CORE DATA
-- =====================================================

-- Insert countries with enhanced data
INSERT INTO countries (name, code, flag_url, timezone, operational_hours, is_active, currency_code, currency_symbol, phone_code) VALUES
('United States', 'US', 'assets/flags/us.png', 'America/New_York', '{"weekdays": {"start": "09:00", "end": "18:00"}, "weekends": {"start": "10:00", "end": "16:00"}}', TRUE, 'USD', '$', '+1'),
('India', 'IN', 'assets/flags/in.png', 'Asia/Kolkata', '{"weekdays": {"start": "10:00", "end": "19:00"}, "weekends": {"start": "11:00", "end": "17:00"}}', TRUE, 'INR', '₹', '+91'),
('United Arab Emirates', 'AE', 'assets/flags/ae.png', 'Asia/Dubai', '{"weekdays": {"start": "08:00", "end": "17:00"}, "weekends": {"start": "09:00", "end": "15:00"}}', TRUE, 'AED', 'د.إ', '+971'),
('China', 'CN', 'assets/flags/cn.png', 'Asia/Shanghai', '{"weekdays": {"start": "09:00", "end": "18:00"}, "weekends": {"start": "10:00", "end": "16:00"}}', TRUE, 'CNY', '¥', '+86'),
('United Kingdom', 'GB', 'assets/flags/gb.png', 'Europe/London', '{"weekdays": {"start": "09:00", "end": "17:30"}, "weekends": {"start": "10:00", "end": "16:00"}}', TRUE, 'GBP', '£', '+44'),
('Canada', 'CA', 'assets/flags/ca.png', 'America/Toronto', '{"weekdays": {"start": "09:00", "end": "17:00"}, "weekends": {"start": "10:00", "end": "16:00"}}', TRUE, 'CAD', 'C$', '+1'),
('Australia', 'AU', 'assets/flags/au.png', 'Australia/Sydney', '{"weekdays": {"start": "09:00", "end": "17:00"}, "weekends": {"start": "10:00", "end": "16:00"}}', TRUE, 'AUD', 'A$', '+61'),
('Germany', 'DE', 'assets/flags/de.png', 'Europe/Berlin', '{"weekdays": {"start": "08:00", "end": "18:00"}, "weekends": {"start": "09:00", "end": "15:00"}}', TRUE, 'EUR', '€', '+49');

-- Insert users with enhanced data
INSERT INTO users (username, email, password, role, country_id, last_active, is_active, timezone, notification_preferences, whatsapp_number, whatsapp_verified, profile_image, bio) VALUES
('admin', 'admin@realtyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), TRUE, 'America/New_York', '{"email": true, "push": true, "whatsapp": false}', '+1234567890', TRUE, 'assets/profiles/admin.jpg', 'System Administrator'),
('agent1', 'agent1@realtyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 1, NOW(), TRUE, 'America/New_York', '{"email": true, "push": true, "whatsapp": true}', '+1234567891', TRUE, 'assets/profiles/agent1.jpg', 'Senior Real Estate Agent'),
('client1', 'client1@realtyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 2, NOW(), TRUE, 'Asia/Kolkata', '{"email": true, "push": false, "whatsapp": true}', '+919876543210', TRUE, 'assets/profiles/client1.jpg', 'Property Buyer'),
('builder1', 'builder1@realtyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'builder', 3, NOW(), TRUE, 'Asia/Dubai', '{"email": true, "push": true, "whatsapp": false}', '+971501234567', FALSE, 'assets/profiles/builder1.jpg', 'Property Developer'),
('influencer1', 'influencer1@realtyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'influencer', 4, NOW(), TRUE, 'Asia/Shanghai', '{"email": true, "push": true, "whatsapp": true}', '+8612345678901', TRUE, 'assets/profiles/influencer1.jpg', 'Real Estate Influencer');

-- Insert properties with enhanced data
INSERT INTO properties (title, description, price, location, property_type, bedrooms, bathrooms, area, agent_id, country_id, views_count, featured, seo_title, seo_description, property_images, amenities) VALUES
('Luxury Villa in Dubai Marina', 'Stunning 4-bedroom villa with sea view', 2500000, 'Dubai Marina, UAE', 'villa', 4, 5, 450, 2, 3, 125, TRUE, 'Luxury Villa Dubai Marina - 4BR Sea View', 'Exclusive 4-bedroom villa in Dubai Marina with stunning sea views', '["villa1_1.jpg", "villa1_2.jpg", "villa1_3.jpg"]', '["pool", "gym", "parking", "security"]'),
('Modern Apartment in Mumbai', '2BR apartment in prime location', 850000, 'Bandra West, Mumbai', 'apartment', 2, 2, 120, 2, 2, 89, FALSE, 'Modern 2BR Apartment Bandra West Mumbai', 'Contemporary 2-bedroom apartment in prime Bandra West location', '["apt1_1.jpg", "apt1_2.jpg"]', '["parking", "security", "garden"]'),
('Downtown NYC Penthouse', 'Luxury penthouse with city views', 3500000, 'Manhattan, NYC', 'penthouse', 3, 3, 280, 2, 1, 156, TRUE, 'Luxury Penthouse Manhattan NYC', 'Exclusive 3-bedroom penthouse with panoramic city views', '["penthouse1_1.jpg", "penthouse1_2.jpg", "penthouse1_3.jpg"]', '["doorman", "gym", "pool", "parking"]'),
('Suburban Family Home', 'Perfect family home with garden', 750000, 'Toronto Suburbs', 'house', 4, 3, 320, 2, 6, 67, FALSE, 'Family Home Toronto Suburbs', 'Spacious 4-bedroom family home with beautiful garden', '["house1_1.jpg", "house1_2.jpg"]', '["garden", "garage", "basement"]');

-- =====================================================
-- ANALYTICS & IP TRACKING DATA
-- =====================================================

-- Insert IP locations
INSERT INTO ip_locations (ip_address, country_code, country_name, city, region, timezone, latitude, longitude, isp) VALUES
('203.0.113.1', 'US', 'United States', 'New York', 'NY', 'America/New_York', 40.7128, -74.0060, 'MockISP'),
('198.51.100.2', 'IN', 'India', 'Mumbai', 'MH', 'Asia/Kolkata', 19.0760, 72.8777, 'MockISP'),
('203.0.113.3', 'AE', 'United Arab Emirates', 'Dubai', 'DU', 'Asia/Dubai', 25.2048, 55.2708, 'MockISP'),
('203.0.113.4', 'CN', 'China', 'Shanghai', 'SH', 'Asia/Shanghai', 31.2304, 121.4737, 'MockISP');

-- Insert user analytics (without referrer column)
INSERT INTO user_analytics (user_id, ip_address, country_code, city, region, timezone, user_agent, page_visited, session_duration, click_count, device_type, browser, os) VALUES
(1, '203.0.113.1', 'US', 'New York', 'NY', 'America/New_York', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '/admin', 120, 5, 'desktop', 'Chrome', 'Windows'),
(2, '198.51.100.2', 'IN', 'Mumbai', 'MH', 'Asia/Kolkata', 'Mozilla/5.0 (Linux; Android 10)', '/client', 90, 3, 'mobile', 'Chrome', 'Android'),
(3, '203.0.113.3', 'AE', 'Dubai', 'DU', 'Asia/Dubai', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15)', '/properties', 180, 8, 'desktop', 'Safari', 'macOS'),
(4, '203.0.113.4', 'CN', 'Shanghai', 'SH', 'Asia/Shanghai', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', '/dashboard', 60, 2, 'mobile', 'Safari', 'iOS');

-- Insert user sessions
INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, started_at, is_active) VALUES
(1, 'session_001', '203.0.113.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', NOW(), TRUE),
(2, 'session_002', '198.51.100.2', 'Mozilla/5.0 (Linux; Android 10)', NOW(), TRUE),
(3, 'session_003', '203.0.113.3', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15)', NOW(), FALSE),
(4, 'session_004', '203.0.113.4', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', NOW(), TRUE);

-- =====================================================
-- TIMEZONE & OPERATIONAL HOURS DATA
-- =====================================================

-- Insert country operational hours (without weekend_operational column)
INSERT INTO country_operational_hours (country_id, timezone, operational_start, operational_end, is_operational) VALUES
(1, 'America/New_York', '09:00:00', '18:00:00', TRUE),
(2, 'Asia/Kolkata', '10:00:00', '19:00:00', TRUE),
(3, 'Asia/Dubai', '08:00:00', '17:00:00', TRUE),
(4, 'Asia/Shanghai', '09:00:00', '18:00:00', TRUE);

-- Insert marketplace status (without status_message column)
INSERT INTO marketplace_status (country_id, current_status, current_time_local, next_operational_time) VALUES
(1, 'operational', '14:00:00', NULL),
(2, 'non-operational', '22:00:00', '2024-01-16 10:00:00'),
(3, 'operational', '12:00:00', NULL),
(4, 'operational', '15:00:00', NULL);

-- =====================================================
-- FEEDBACK & TICKET DATA
-- =====================================================

-- Insert feedback tickets (using existing user IDs)
INSERT INTO feedback_tickets (user_id, name, email, subject, message, category, priority, status) VALUES
(2, 'Alice Agent', 'agent1@realtyflow.com', 'Bug: Dashboard not loading', 'The agent dashboard fails to load after login.', 'bug', 'high', 'open'),
(3, 'Bob Client', 'client1@realtyflow.com', 'Feature: Add property filter', 'Please add a filter for property type.', 'feature_request', 'medium', 'open'),
(4, 'Charlie Builder', 'builder1@realtyflow.com', 'General: Account verification', 'Need help with account verification process.', 'general', 'low', 'in_progress'),
(5, 'Diana Influencer', 'influencer1@realtyflow.com', 'Support: Payment issues', 'Having trouble with payment processing.', 'support', 'high', 'open');

-- Insert ticket responses
INSERT INTO ticket_responses (ticket_id, user_id, message, is_internal) VALUES
(1, 1, 'Thank you for reporting this issue. We are investigating the dashboard loading problem.', FALSE),
(1, NULL, 'Internal note: Dashboard issue confirmed, working on fix', TRUE),
(2, 1, 'Thank you for the feature request. We will consider adding property type filters.', FALSE),
(3, 1, 'We are processing your account verification. Please allow 24-48 hours.', FALSE);

-- =====================================================
-- USER ACTIVITY & INACTIVITY DATA
-- =====================================================

-- Insert user activity
INSERT INTO user_activity (user_id, last_login, last_active, session_duration, pages_visited, actions_performed) VALUES
(1, NOW(), NOW(), 3600, 15, 25),
(2, NOW(), NOW(), 1800, 8, 12),
(3, NOW(), NOW(), 1200, 5, 8),
(4, NOW(), NOW(), 2400, 12, 18),
(5, NOW(), NOW(), 900, 3, 5);

-- Insert inactivity notifications
INSERT INTO inactivity_notifications (user_id, notification_type, sent_at, notification_method, message, is_read) VALUES
(3, '30_days', DATE_SUB(NOW(), INTERVAL 5 DAY), 'email', 'We miss you! Check out our latest properties.', FALSE),
(5, '90_days', DATE_SUB(NOW(), INTERVAL 10 DAY), 'whatsapp', 'Special offers for returning users!', TRUE);

-- =====================================================
-- PWA & NOTIFICATION DATA
-- =====================================================

-- Insert push subscriptions
INSERT INTO push_subscriptions (user_id, endpoint, p256dh_key, auth_token, created_at) VALUES
(1, 'https://fcm.googleapis.com/fcm/send/mock_endpoint_1', 'mock_p256dh_key_1', 'mock_auth_token_1', NOW()),
(2, 'https://fcm.googleapis.com/fcm/send/mock_endpoint_2', 'mock_p256dh_key_2', 'mock_auth_token_2', NOW()),
(3, 'https://fcm.googleapis.com/fcm/send/mock_endpoint_3', 'mock_p256dh_key_3', 'mock_auth_token_3', NOW());

-- Insert notifications
INSERT INTO notifications (user_id, title, message, type, is_read, created_at) VALUES
(1, 'New Property Alert', 'A new luxury villa has been added in your area', 'property_alert', FALSE, NOW()),
(2, 'Lead Update', 'New lead assigned to you', 'lead_update', FALSE, NOW()),
(3, 'System Maintenance', 'Scheduled maintenance on Sunday 2-4 AM', 'system', TRUE, NOW()),
(4, 'Payment Success', 'Your payment has been processed successfully', 'payment', FALSE, NOW()),
(5, 'Welcome Back', 'Welcome back to RealtyFlow Pro!', 'welcome', TRUE, NOW());

-- =====================================================
-- PROPERTY ANALYTICS DATA
-- =====================================================

-- Insert property views
INSERT INTO property_views (property_id, user_id, ip_address, viewed_at, session_id) VALUES
(1, 2, '198.51.100.2', NOW(), 'session_002'),
(1, 3, '203.0.113.3', NOW(), 'session_003'),
(2, 4, '203.0.113.4', NOW(), 'session_004'),
(3, 2, '198.51.100.2', NOW(), 'session_002'),
(4, 3, '203.0.113.3', NOW(), 'session_003');

-- Insert property favorites
INSERT INTO property_favorites (property_id, user_id, created_at) VALUES
(1, 3, NOW()),
(2, 4, NOW()),
(3, 2, NOW()),
(4, 5, NOW());

-- =====================================================
-- WHATSAPP INTEGRATION DATA
-- =====================================================

-- Insert WhatsApp integrations
INSERT INTO whatsapp_integrations (user_id, phone_number, is_verified, verification_code, last_message_sent, created_at) VALUES
(1, '+1234567890', TRUE, NULL, NOW(), NOW()),
(2, '+919876543210', TRUE, NULL, NOW(), NOW()),
(3, '+971501234567', FALSE, '123456', NULL, NOW()),
(4, '+8612345678901', TRUE, NULL, NOW(), NOW()),
(5, '+1234567890', TRUE, NULL, NOW(), NOW());

-- =====================================================
-- LEADS DATA
-- =====================================================

-- Insert leads
INSERT INTO leads (name, email, phone, property_id, agent_id, status, source, notes, created_at) VALUES
('John Smith', 'john@example.com', '+1234567890', 1, 2, 'new', 'website', 'Interested in luxury properties', NOW()),
('Sarah Johnson', 'sarah@example.com', '+1987654321', 2, 2, 'contacted', 'referral', 'Looking for investment property', NOW()),
('Mike Chen', 'mike@example.com', '+861234567890', 3, 2, 'qualified', 'social_media', 'High net worth individual', NOW()),
('Emma Wilson', 'emma@example.com', '+919876543210', 4, 2, 'converted', 'direct', 'Property purchased successfully', NOW());

-- =====================================================
-- SAMPLE TIMEZONE DATA FOR TESTING
-- =====================================================

-- Insert sample timezone data for testing operational hours
INSERT INTO marketplace_status (country_id, current_status, current_time_local, next_operational_time) VALUES
(5, 'operational', '10:30:00', NULL),
(6, 'non-operational', '23:00:00', '2024-01-16 09:00:00'),
(7, 'operational', '11:45:00', NULL),
(8, 'limited', '16:30:00', '2024-01-16 08:00:00'); 