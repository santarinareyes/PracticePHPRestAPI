CREATE TABLE categories (
category_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
category_title VARCHAR(255) NOT NULL UNIQUE
)
ENGINE = InnoDb;

CREATE TABLE products (
product_id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
product_category_id INT,
product_title VARCHAR(255) NOT NULL,
product_price DOUBLE(9,2),
product_description TEXT,
product_created DATETIME DEFAULT CURRENT_TIMESTAMP,
product_updated DATETIME ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (product_category_id) REFERENCES categories(category_id) ON DELETE SET NULL
)
ENGINE = InnoDb;

CREATE TABLE users (
user_id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(255) NOT NULL,
lastname VARCHAR(255) NOT NULL,
username VARCHAR(255) UNIQUE NOT NULL,
email VARCHAR(255) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL COLLATE utf8_bin,
role VARCHAR(255) DEFAULT 'User',
user_created DATETIME DEFAULT CURRENT_TIMESTAMP,
user_updated DATETIME ON UPDATE CURRENT_TIMESTAMP,
isactive ENUM ("Y", "N") DEFAULT "Y",
loginattempts INT(1) DEFAULT 0
)
Engine = InnoDb;

CREATE TABLE sessions (
session_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
session_user_id BIGINT,
accesstoken VARCHAR(100) COLLATE utf8_bin UNIQUE,
accesstoken_expiry DATETIME,
refreshtoken VARCHAR(100) COLLATE utf8_bin UNIQUE,
refreshtoken_expiry DATETIME,
FOREIGN KEY (session_user_id) REFERENCES users(user_id) ON DELETE CASCADE
)
Engine = InnoDb;

CREATE TABLE carts (
cart_id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
cart_user_id BIGINT,
cart_product_id BIGINT,
product_added DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (cart_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (cart_product_id) REFERENCES products(product_id) ON DELETE CASCADE
)
Engine = InnoDb;