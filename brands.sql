CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    brand_name VARCHAR(255) NOT NULL,
    pic_name VARCHAR(255) NOT NULL,
    pic_phone VARCHAR(20) NOT NULL,
    province VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    referral_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
