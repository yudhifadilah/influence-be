CREATE DATABASE starpowers;

USE starpowers;

CREATE TABLE faqs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(50) NOT NULL,
  question TEXT NOT NULL,
  answer TEXT NOT NULL
);