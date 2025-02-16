CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  influencer_id INT NOT NULL,
  service_name VARCHAR(255) NOT NULL,
  price_per_post DECIMAL(10, 2) NOT NULL,
  description TEXT NOT NULL,
  duration INT NOT NULL,
  FOREIGN KEY (influencer_id) REFERENCES influencers(id)
);