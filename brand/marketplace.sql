CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);


CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    influencer_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    proposal_deadline DATE NOT NULL,
    brief TEXT NOT NULL,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (influencer_id) REFERENCES influencers(id)
);

CREATE TABLE IF NOT EXISTS campaigns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category_id INT NOT NULL,
  influencers JSON NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  proposal_deadline DATE NOT NULL,
  brief TEXT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending'
);