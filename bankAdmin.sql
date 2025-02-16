CREATE TABLE bank_accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_type VARCHAR(255) NOT NULL,
  account_number VARCHAR(255) NOT NULL,
  account_holder VARCHAR(255) NOT NULL
);