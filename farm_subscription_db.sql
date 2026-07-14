-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS farm_subscription_db;
USE farm_subscription_db;

-- جدول المشتركين
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT NOT NULL,
    subscription_type ENUM('أسبوعي', 'شهري', 'ربع سنوي') DEFAULT 'أسبوعي',
    subscription_status ENUM('نشط', 'موقف', 'ملغى') DEFAULT 'نشط',
    start_date DATE NOT NULL,
    payment_method ENUM('بطاقة ائتمان', 'تحويل بنكي', 'نقدي عند الاستلام') DEFAULT 'بطاقة ائتمان',
    box_preferences TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- إضافة بعض المشتركين الافتراضيين
INSERT INTO subscribers (full_name, email, phone, address, subscription_type, subscription_status, start_date, payment_method, box_preferences) VALUES
('محمد أحمد', 'mohamed@example.com', '0551234567', 'الرياض، حي العليا، شارع الملك فهد', 'أسبوعي', 'نشط', '2023-10-01', 'بطاقة ائتمان', 'خضروات وفواكه طازجة'),
('سارة عبدالله', 'sara@example.com', '0557654321', 'جدة، حي الصفا، شارع الأمير سلطان', 'شهري', 'نشط', '2023-09-15', 'تحويل بنكي', 'فواكه عضوية فقط'),
('خالد السعدي', 'khaled@example.com', '0569876543', 'الدمام، حي الجلوية، شارع الملك عبدالله', 'ربع سنوي', 'نشط', '2023-08-20', 'نقدي عند الاستلام', 'خضروات متنوعة'),
('فاطمة العتيبي', 'fatima@example.com', '0541237890', 'الرياض، حي النخيل، شارع العروبة', 'أسبوعي', 'موقف', '2023-10-10', 'بطاقة ائتمان', 'منتجات ألبان مع الخضار'),
('نورة الشمري', 'noura@example.com', '0555551234', 'مكة، حي العزيزية، شارع الستين', 'شهري', 'ملغى', '2023-07-05', 'تحويل بنكي', 'فواكه استوائية');