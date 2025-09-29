CREATE TABLE users (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(80)  NOT NULL,
  last_name     VARCHAR(80)  NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('user','admin') NOT NULL DEFAULT 'user',
  is_active     INT not null,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE cars (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  model           VARCHAR(80)  NOT NULL,
  year            SMALLINT     NOT NULL,
  price_per_day   DECIMAL(10,2) NOT NULL,
  is_active       TINYINT(1)   NOT NULL DEFAULT 1,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE locations (
  id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name    VARCHAR(120) NOT NULL,
  address VARCHAR(190) NULL,
  city    VARCHAR(80)  NOT NULL,
  country VARCHAR(80)  NOT NULL
);


CREATE TABLE reservations (
  id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id              BIGINT UNSIGNED NOT NULL,
  car_id               BIGINT UNSIGNED NOT NULL,
  pickup_location_id   BIGINT UNSIGNED NOT NULL,
  return_location_id   BIGINT UNSIGNED NOT NULL,
  pickup_dt            DATETIME NOT NULL,
  return_dt            DATETIME NOT NULL,
  status               ENUM('PENDING','CONFIRMED','CANCELLED','COMPLETED') NOT NULL DEFAULT 'PENDING',
  total_price          DECIMAL(10,2) NOT NULL,
  created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE RESTRICT,
  FOREIGN KEY (pickup_location_id) REFERENCES locations(id) ON DELETE RESTRICT,
  FOREIGN KEY (return_location_id) REFERENCES locations(id) ON DELETE RESTRICT,
  INDEX idx_res_car_dates (car_id, pickup_dt, return_dt),
  INDEX idx_res_user (user_id)
);


ALTER TABLE reservations
DROP FOREIGN KEY reservations_ibfk_1;

ALTER TABLE reservations
ADD CONSTRAINT reservations_ibfk_1
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;



