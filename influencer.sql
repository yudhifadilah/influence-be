CREATE DATABASE starpowers;

USE starpowers;

CREATE TABLE influencers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  birth_date DATE NOT NULL,
  gender VARCHAR(50) NOT NULL,
  influencer_category VARCHAR(255) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  referral_code VARCHAR(50),
  ktp_number VARCHAR(50) NOT NULL,
  npwp_number VARCHAR(50) NOT NULL,
  instagram_link VARCHAR(255) NOT NULL,
  followers_count INT NOT NULL,
  profile_picture VARCHAR(255),
  bank_account VARCHAR(255),
  account_number VARCHAR(255),
  province VARCHAR(255), -- Add this line
  city VARCHAR(255), -- Add this line
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);