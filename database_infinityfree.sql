SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS user_notifications;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_status_logs;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS product_promotions;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) UNIQUE,

    gender ENUM('male','female','other') NULL,
    birthday DATE NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,

    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role ENUM('user', 'staff', 'admin') NOT NULL DEFAULT 'user',
    status ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    rank_level ENUM('silver', 'gold', 'diamond') NOT NULL DEFAULT 'silver',
    total_spent DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT,
    gender ENUM('male', 'female', 'unisex') NOT NULL DEFAULT 'unisex',
    concentration VARCHAR(50),
    volume VARCHAR(50),
    scent_group VARCHAR(100),
    top_note VARCHAR(255),
    middle_note VARCHAR(255),
    base_note VARCHAR(255),
    fragrance_story TEXT,
    longevity VARCHAR(100),
    occasion VARCHAR(150),
    price DECIMAL(12,2) NOT NULL,
    sale_price DECIMAL(12,2),
    stock INT NOT NULL DEFAULT 0,
    sold_quantity INT NOT NULL DEFAULT 0,
    main_image VARCHAR(255),
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_products_brand
        FOREIGN KEY (brand_id) REFERENCES brands(id)
        ON UPDATE CASCADE,
    INDEX idx_products_name (name),
    INDEX idx_products_price (price),
    INDEX idx_products_status (status)
) ENGINE=InnoDB;

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_images_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    discount_type ENUM('percent', 'fixed') NOT NULL,
    discount_value DECIMAL(12,2) NOT NULL,
    min_order_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    max_discount DECIMAL(12,2),
    quantity INT NOT NULL DEFAULT 0,
    used_quantity INT NOT NULL DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE product_promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    promotion_id INT NOT NULL,
    CONSTRAINT fk_product_promotions_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_product_promotions_promotion
        FOREIGN KEY (promotion_id) REFERENCES promotions(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    UNIQUE KEY uq_product_promotion (product_id, promotion_id)
) ENGINE=InnoDB;

CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_carts_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_items_cart
        FOREIGN KEY (cart_id) REFERENCES carts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_cart_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE,
    UNIQUE KEY uq_cart_product (cart_id, product_id)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    voucher_id INT NULL,
    order_code VARCHAR(30) NOT NULL UNIQUE,
    receiver_name VARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20) NOT NULL,
    receiver_email VARCHAR(100),
    receiver_address TEXT NOT NULL,
    note TEXT,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('cod', 'bank_transfer') NOT NULL DEFAULT 'cod',
    payment_status ENUM('unpaid', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'unpaid',
    status ENUM('pending', 'confirmed', 'shipping', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    cancel_reason TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_orders_voucher
        FOREIGN KEY (voucher_id) REFERENCES vouchers(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    INDEX idx_orders_status (status),
    INDEX idx_orders_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    product_image VARCHAR(255),
    price DECIMAL(12,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE,
    INDEX idx_order_items_product (product_id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    amount DECIMAL(12,2) NOT NULL,
    method ENUM('cod', 'bank_transfer') NOT NULL,
    status ENUM('unpaid', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'unpaid',
    transaction_code VARCHAR(100),
    paid_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'shipping', 'delivered', 'completed', 'cancelled') NOT NULL,
    note VARCHAR(255),
    changed_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_status_logs_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_order_status_logs_user
        FOREIGN KEY (changed_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5),
    UNIQUE KEY uq_user_product_order_review (user_id, product_id, order_id)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('email', 'system') NOT NULL DEFAULT 'email',
    sent_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_sender
        FOREIGN KEY (sent_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    user_id INT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    CONSTRAINT fk_user_notifications_notification
        FOREIGN KEY (notification_id) REFERENCES notifications(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_user_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    UNIQUE KEY uq_notification_user (notification_id, user_id)
) ENGINE=InnoDB;

INSERT INTO users (
    full_name,
    email,
    phone,
    password,
    role,
    status,
    rank_level
)
VALUES
(
    'Super Admin',
    'admin@kvperfume.com',
    '0909999999',
    '$2y$12$.cdIUL/Zn1TTk29loVf3Q.yjRFGBFztwlmC7UEgEjrOo8PaIiYVhO',
    'admin',
    'active',
    'diamond'
),
(
    'Nhân Viên Demo',
    'staff@kvperfume.com',
    '0900000002',
    '$2y$10$VQ6iD1Sx7WQ4Xh8BvGm3uOg3hN9M7sL1M0fD6R5uS1YcR2P0e7A6W',
    'staff',
    'active',
    'gold'
),
(
    'Khách Hàng Demo',
    'user@kvperfume.com',
    '0900000003',
    '$2y$10$VQ6iD1Sx7WQ4Xh8BvGm3uOg3hN9M7sL1M0fD6R5uS1YcR2P0e7A6W',
    'user',
    'active',
    'silver'
);

INSERT INTO categories (name, slug, description)
VALUES
('Nước hoa nữ', 'nuoc-hoa-nu', 'Sản phẩm nước hoa dành cho nữ'),
('Nước hoa nam', 'nuoc-hoa-nam', 'Sản phẩm nước hoa dành cho nam'),
('Nước hoa unisex', 'nuoc-hoa-unisex', 'Mùi hương phù hợp cho mọi giới tính'),
('Gift set', 'gift-set', 'Bộ quà tặng nước hoa cao cấp');

INSERT INTO brands (name, slug, description)
VALUES
('Chanel', 'chanel', 'Thương hiệu nước hoa cao cấp'),
('Dior', 'dior', 'Thương hiệu nước hoa Pháp'),
('Yves Saint Laurent', 'ysl', 'Thương hiệu thời trang và nước hoa cao cấp'),
('Gucci', 'gucci', 'Thương hiệu thời trang và nước hoa Ý');

INSERT INTO products (
    category_id, brand_id, name, slug, description, gender, concentration,
    volume, scent_group, price, sale_price, stock, main_image
)
VALUES
(1, 1, 'Chanel N5 Eau de Parfum', 'chanel-n5-eau-de-parfum', 'Hương hoa cổ điển, sang trọng và nữ tính.', 'female', 'EDP', '100ml', 'Floral Aldehyde', 4200000, 3990000, 20, '/public/upload/N5DEPARFUM.webp'),
(1, 1, 'Coco Mademoiselle', 'coco-mademoiselle', 'Mùi hương thanh lịch với cam, hoa hồng và hoắc hương.', 'female', 'EDP', '100ml', 'Oriental Floral', 4500000, NULL, 15, '/public/upload/Coco Mademoiselle.avif'),
(2, 2, 'Dior Sauvage', 'dior-sauvage', 'Mùi hương nam tính, tươi mát và mạnh mẽ.', 'male', 'EDT', '100ml', 'Aromatic Fougere', 3300000, 3100000, 25, '/public/upload/dior-sauvage.webp'),
(3, 3, 'YSL Libre', 'ysl-libre', 'Mùi hương hiện đại, tự do và cá tính.', 'unisex', 'EDP', '90ml', 'Floral Lavender', 3900000, NULL, 18, '/public/upload/ysl-libre.webp'),
(4, 4, 'Gucci Bloom Gift Set', 'gucci-bloom-gift-set', 'Bộ quà tặng nước hoa Gucci Bloom.', 'female', 'EDP', 'Set', 'White Floral', 5200000, 4890000, 10, '/public/upload/gucci-bloom-gift-set.webp');

INSERT INTO products (
    category_id, brand_id, name, slug, description, gender, concentration,
    volume, scent_group, price, sale_price, stock, main_image
)
VALUES
(1, 1, 'Chanel Chance Eau Tendre', 'chanel-chance-eau-tendre', 'Hương thơm mềm mại với bưởi, hoa nhài và xạ hương trắng.', 'female', 'EDT', '100ml', 'Floral Fruity', 3950000, 3690000, 18, '/public/upload/N5DEPARFUM.webp'),
(1, 1, 'Chanel Gabrielle Essence', 'chanel-gabrielle-essence', 'Mùi hương hoa trắng sang trọng, nổi bật với hoa nhài và hoa cam.', 'female', 'EDP', '100ml', 'White Floral', 4300000, NULL, 14, '/public/upload/N5DETOILETTE.webp'),
(2, 1, 'Bleu de Chanel Parfum', 'bleu-de-chanel-parfum', 'Hương gỗ nam tính, sâu lắng với gỗ đàn hương và hổ phách.', 'male', 'Parfum', '100ml', 'Woody Aromatic', 4550000, 4290000, 20, '/public/upload/N5LEAU.webp'),
(2, 1, 'Allure Homme Sport', 'allure-homme-sport', 'Mùi hương tươi mát, năng động với cam chanh và xạ hương.', 'male', 'EDT', '100ml', 'Fresh Spicy', 3500000, NULL, 22, '/public/upload/N5LEAUU1.webp'),
(3, 1, 'Chanel Paris-Biarritz', 'chanel-paris-biarritz', 'Hương cam chanh tươi sáng, thanh lịch và dễ dùng hằng ngày.', 'unisex', 'EDT', '125ml', 'Fresh Citrus', 3900000, 3650000, 16, '/public/upload/N5DETOILETTE1.webp'),
(1, 2, 'Dior J adore Eau de Parfum', 'dior-jadore-eau-de-parfum', 'Hương hoa nữ tính, rực rỡ với hoa nhài, hoa hồng và ngọc lan tây.', 'female', 'EDP', '100ml', 'Floral', 4100000, 3890000, 21, '/public/upload/N5DEPARFUM.webp'),
(2, 2, 'Dior Homme Intense', 'dior-homme-intense', 'Mùi hương nam tính ấm áp với iris, gỗ tuyết tùng và hổ phách.', 'male', 'EDP', '100ml', 'Woody Floral Musk', 3850000, NULL, 13, '/public/upload/N5DETOILETTE.webp'),
(3, 2, 'Dior Gris Dior', 'dior-gris-dior', 'Mùi hương unisex tinh tế với hoa hồng, hoắc hương và gỗ.', 'unisex', 'EDP', '125ml', 'Chypre Floral', 6200000, 5890000, 8, '/public/upload/N5LEAU.webp'),
(2, 2, 'Dior Fahrenheit', 'dior-fahrenheit', 'Hương da thuộc, gỗ và hoa violet tạo cảm giác mạnh mẽ.', 'male', 'EDT', '100ml', 'Leather Woody', 3200000, 2990000, 17, '/public/upload/N5LEAUU1.webp'),
(1, 2, 'Miss Dior Blooming Bouquet', 'miss-dior-blooming-bouquet', 'Hương hoa hồng và mẫu đơn nhẹ nhàng, trẻ trung.', 'female', 'EDT', '100ml', 'Floral Fresh', 3600000, NULL, 24, '/public/upload/N5DETOILETTE1.webp'),
(3, 3, 'YSL Black Opium', 'ysl-black-opium', 'Mùi hương cà phê, vani và hoa trắng quyến rũ, hiện đại.', 'female', 'EDP', '90ml', 'Oriental Vanilla', 3650000, 3390000, 19, '/public/upload/N5DEPARFUM.webp'),
(2, 3, 'YSL Y Eau de Parfum', 'ysl-y-eau-de-parfum', 'Hương nam tươi mát với táo xanh, xô thơm và gỗ ấm.', 'male', 'EDP', '100ml', 'Aromatic Fougere', 3400000, NULL, 20, '/public/upload/N5DETOILETTE.webp'),
(3, 3, 'YSL La Nuit de L Homme', 'ysl-la-nuit-de-l-homme', 'Mùi hương cay ấm, lịch lãm với bạch đậu khấu và lavender.', 'male', 'EDT', '100ml', 'Warm Spicy', 3100000, 2890000, 15, '/public/upload/N5LEAU.webp'),
(1, 3, 'YSL Mon Paris', 'ysl-mon-paris', 'Hương trái cây ngọt ngào với dâu tây, lê và hoắc hương.', 'female', 'EDP', '90ml', 'Fruity Floral', 3450000, NULL, 16, '/public/upload/N5LEAUU1.webp'),
(3, 3, 'YSL Libre Intense', 'ysl-libre-intense', 'Mùi hương lavender, hoa cam và vani ấm áp, cá tính.', 'unisex', 'EDP', '90ml', 'Floral Lavender', 4100000, 3850000, 12, '/public/upload/N5DETOILETTE1.webp'),
(1, 4, 'Gucci Flora Gorgeous Gardenia', 'gucci-flora-gorgeous-gardenia', 'Hương hoa dành dành, lê và đường nâu ngọt dịu.', 'female', 'EDP', '100ml', 'Floral Fruity', 3400000, 3150000, 18, '/public/upload/N5DEPARFUM.webp'),
(3, 4, 'Gucci Guilty Pour Femme', 'gucci-guilty-pour-femme', 'Mùi hương hoa cổ điển pha chút hiện đại và quyến rũ.', 'female', 'EDP', '90ml', 'Oriental Floral', 3300000, NULL, 15, '/public/upload/N5DETOILETTE.webp'),
(2, 4, 'Gucci Guilty Pour Homme', 'gucci-guilty-pour-homme', 'Hương lavender, chanh và gỗ tuyết tùng nam tính.', 'male', 'EDT', '90ml', 'Aromatic Woody', 3000000, 2790000, 20, '/public/upload/N5LEAU.webp'),
(3, 4, 'Gucci Memoire d une Odeur', 'gucci-memoire-d-une-odeur', 'Mùi hương unisex với hoa cúc La Mã, nhài và gỗ đàn hương.', 'unisex', 'EDP', '100ml', 'Herbal Woody', 3500000, NULL, 11, '/public/upload/N5LEAUU1.webp'),
(4, 4, 'Gucci Flora Gift Set', 'gucci-flora-gift-set', 'Bộ quà tặng nước hoa Gucci Flora kèm sản phẩm chăm sóc cơ thể.', 'female', 'EDP', 'Set', 'Floral Gift', 5400000, 4990000, 9, '/public/upload/N5DETOILETTE1.webp');

-- =========================================================
-- CẬP NHẬT ẢNH, MÔ TẢ VÀ CÁC TẦNG HƯƠNG CÓ DẤU TIẾNG VIỆT
-- Đặt sau INSERT products để import một lần là dùng được.
-- =========================================================

UPDATE products SET main_image = '/public/upload/N5DEPARFUM.webp' WHERE slug = 'chanel-n5-eau-de-parfum';
UPDATE products SET main_image = '/public/upload/Coco Mademoiselle.avif' WHERE slug = 'coco-mademoiselle';
UPDATE products SET main_image = '/public/upload/dior-sauvage.webp' WHERE slug = 'dior-sauvage';
UPDATE products SET main_image = '/public/upload/ysl-libre.webp' WHERE slug = 'ysl-libre';
UPDATE products SET main_image = '/public/upload/gucci-bloom-gift-set.webp' WHERE slug = 'gucci-bloom-gift-set';
UPDATE products SET main_image = '/public/upload/chanel-chance-eau-tendre.webp' WHERE slug = 'chanel-chance-eau-tendre';
UPDATE products SET main_image = '/public/upload/Chanel Gabrielle Essence.webp' WHERE slug = 'chanel-gabrielle-essence';
UPDATE products SET main_image = '/public/upload/Allure Homme Sport.jpg' WHERE slug = 'allure-homme-sport';
UPDATE products SET main_image = '/public/upload/Chanel Paris-Biarritz.avif' WHERE slug = 'chanel-paris-biarritz';
UPDATE products SET main_image = '/public/upload/Dior J adore Eau de Parfum.jpg' WHERE slug = 'dior-jadore-eau-de-parfum';
UPDATE products SET main_image = '/public/upload/dior-homme-intense.webp' WHERE slug = 'dior-homme-intense';
UPDATE products SET main_image = '/public/upload/Dior Gris Dior.jpg' WHERE slug = 'dior-gris-dior';
UPDATE products SET main_image = '/public/upload/Dior Fahrenheit.jpg' WHERE slug = 'dior-fahrenheit';
UPDATE products SET main_image = '/public/upload/Miss Dior Blooming Bouquet.jpg' WHERE slug = 'miss-dior-blooming-bouquet';

UPDATE products SET
    description = 'Hương hoa cổ điển, sang trọng và nữ tính.',
    top_note = 'Aldehyde, hoa cam neroli, quýt bergamot',
    middle_note = 'Hoa nhài, hoa hồng May, linh lan, ngọc lan tây',
    base_note = 'Gỗ đàn hương, cỏ vetiver, vani, xạ hương trắng',
    fragrance_story = 'Hương hoa aldehyde kinh điển, sang trọng và mềm mại. Mùi hương mở đầu sáng như một lớp lụa trắng, sau đó chuyển thành cảm giác nữ tính, thanh lịch và rất Chanel.',
    longevity = '7 - 10 giờ',
    occasion = 'Dự tiệc, hẹn hò, sự kiện trang trọng'
WHERE slug = 'chanel-n5-eau-de-parfum';

UPDATE products SET
    description = 'Mùi hương thanh lịch với cam, hoa hồng và hoắc hương.',
    top_note = 'Cam Sicily, quýt bergamot, cam ngọt',
    middle_note = 'Hoa hồng, hoa nhài, hoa mimosa',
    base_note = 'Hoắc hương, cỏ vetiver, vani, xạ hương trắng',
    fragrance_story = 'Mùi hương thanh lịch và hiện đại, vừa tươi sáng vừa quyến rũ. Cam chanh tạo cảm giác trẻ trung, lớp nền hoắc hương và vani giúp mùi hương sang hơn và có chiều sâu.',
    longevity = '8 - 12 giờ',
    occasion = 'Đi làm, đi chơi, hẹn hò, tiệc tối'
WHERE slug = 'coco-mademoiselle';

UPDATE products SET
    description = 'Mùi hương nam tính, tươi mát và mạnh mẽ.',
    top_note = 'Cam bergamot, tiêu Tứ Xuyên',
    middle_note = 'Oải hương, phong lữ, tiêu hồng',
    base_note = 'Ambroxan, gỗ tuyết tùng, labdanum',
    fragrance_story = 'Mùi hương nam tính, sạch và mạnh mẽ. Sauvage tạo ấn tượng tươi mát ngay từ đầu, sau đó chuyển sang nét cay ấm và khoáng đạt.',
    longevity = '7 - 9 giờ',
    occasion = 'Đi làm, gặp gỡ, hẹn hò, dùng hằng ngày'
WHERE slug = 'dior-sauvage';

UPDATE products SET
    description = 'Mùi hương hiện đại, tự do và cá tính.',
    top_note = 'Quýt mandarin, hoa cam neroli, lavender',
    middle_note = 'Hoa cam, hoa nhài sambac',
    base_note = 'Vani Madagascar, xạ hương, gỗ tuyết tùng',
    fragrance_story = 'Libre là mùi hương của sự tự do: lavender tạo nét cá tính, hoa cam làm mùi hương mềm và sang, vani giữ lại độ ấm áp quyến rũ.',
    longevity = '7 - 10 giờ',
    occasion = 'Đi làm, hẹn hò, dự tiệc nhẹ'
WHERE slug = 'ysl-libre';

UPDATE products SET
    description = 'Bộ quà tặng nước hoa Gucci Bloom cao cấp.',
    top_note = 'Hoa nhài Rangoon creeper, cam xanh',
    middle_note = 'Hoa huệ tuberose, hoa nhài sambac',
    base_note = 'Xạ hương, gỗ trắng, hương phấn nhẹ',
    fragrance_story = 'Bộ quà tặng Gucci Bloom mang sắc hoa trắng mềm mại, nữ tính và gần gũi. Mùi hương tạo cảm giác như một khu vườn hoa đang nở trong buổi sáng.',
    longevity = '6 - 8 giờ',
    occasion = 'Quà tặng, đi chơi, đi làm, hẹn hò'
WHERE slug = 'gucci-bloom-gift-set';

UPDATE products SET
    description = 'Hương thơm mềm mại với bưởi, hoa nhài và xạ hương trắng.',
    top_note = 'Bưởi, quả mộc qua',
    middle_note = 'Hoa nhài, lan dạ hương',
    base_note = 'Xạ hương trắng, hổ phách mềm',
    fragrance_story = 'Chance Eau Tendre nhẹ nhàng, trong trẻo và nữ tính. Hương bưởi mở đầu tươi sáng, hoa nhài làm mùi hương mềm hơn, xạ hương trắng để lại cảm giác sạch sẽ.',
    longevity = '5 - 7 giờ',
    occasion = 'Đi học, đi làm, cà phê, ban ngày'
WHERE slug = 'chanel-chance-eau-tendre';

UPDATE products SET
    description = 'Mùi hương hoa trắng sang trọng, nổi bật với hoa nhài và hoa cam.',
    top_note = 'Cam chanh, trái cây đỏ, lá xanh',
    middle_note = 'Hoa nhài, hoa cam, ngọc lan tây, tuberose',
    base_note = 'Gỗ đàn hương, xạ hương trắng, vani',
    fragrance_story = 'Gabrielle Essence là bó hoa trắng đầy đặn và sang trọng. Mùi hương mở ra ấm áp, sau đó bung nở thành cảm giác mềm mại, nữ tính và trưởng thành.',
    longevity = '7 - 9 giờ',
    occasion = 'Dự tiệc, đi làm, gặp mặt quan trọng'
WHERE slug = 'chanel-gabrielle-essence';

UPDATE products SET
    description = 'Hương gỗ nam tính, sâu lắng với gỗ đàn hương và hổ phách.',
    top_note = 'Chanh, cam bergamot, bạc hà',
    middle_note = 'Lavender, phong lữ, dứa thơm',
    base_note = 'Gỗ đàn hương, gỗ tuyết tùng, hổ phách, đậu tonka',
    fragrance_story = 'Bleu de Chanel Parfum có nét xanh trầm, lịch lãm và sâu hơn bản EDT. Mùi hương phù hợp với người thích sự gọn gàng, nam tính và sang trọng.',
    longevity = '8 - 12 giờ',
    occasion = 'Công việc, gặp đối tác, hẹn hò tối'
WHERE slug = 'bleu-de-chanel-parfum';

UPDATE products SET
    description = 'Mùi hương tươi mát, năng động với cam chanh và xạ hương.',
    top_note = 'Cam mandarin, cam biển, aldehyde',
    middle_note = 'Tiêu đen, hoa cam neroli, tuyết tùng',
    base_note = 'Xạ hương trắng, đậu tonka, hổ phách',
    fragrance_story = 'Allure Homme Sport tươi mát và năng động, có cảm giác thể thao nhưng vẫn sang. Lớp cam chanh sảng khoái kết hợp nền xạ hương ấm áp.',
    longevity = '6 - 8 giờ',
    occasion = 'Hằng ngày, đi làm, thể thao, du lịch'
WHERE slug = 'allure-homme-sport';

UPDATE products SET
    description = 'Hương cam chanh tươi sáng, thanh lịch và dễ dùng hằng ngày.',
    top_note = 'Quýt mandarin, bưởi, cam bergamot',
    middle_note = 'Hoa linh lan, neroli, hương xanh',
    base_note = 'Xạ hương trắng, cỏ vetiver',
    fragrance_story = 'Paris-Biarritz là mùi hương cam chanh sạch, nhẹ và thanh lịch. Cảm giác như gió biển mùa hè, rất hợp khi cần một mùi hương dễ chịu.',
    longevity = '4 - 6 giờ',
    occasion = 'Ban ngày, mùa hè, đi làm, du lịch'
WHERE slug = 'chanel-paris-biarritz';

UPDATE products SET
    description = 'Hương hoa nữ tính, rực rỡ với hoa nhài, hoa hồng và ngọc lan tây.',
    top_note = 'Hoa ngọc lan tây, lê, đào',
    middle_note = 'Hoa nhài, hoa hồng damascena, linh lan',
    base_note = 'Xạ hương, vani, gỗ tuyết tùng',
    fragrance_story = 'J adore rực rỡ và nữ tính, như một bó hoa vàng sang trọng. Mùi hương mềm, sang và dễ tạo ấn tượng trong không gian gần.',
    longevity = '7 - 10 giờ',
    occasion = 'Hẹn hò, tiệc, sự kiện, đi làm'
WHERE slug = 'dior-jadore-eau-de-parfum';

UPDATE products SET
    description = 'Mùi hương nam tính ấm áp với iris, gỗ tuyết tùng và hổ phách.',
    top_note = 'Lavender, xô thơm, cam bergamot',
    middle_note = 'Iris, hổ phách, cacao',
    base_note = 'Gỗ tuyết tùng, cỏ vetiver, da thuộc',
    fragrance_story = 'Dior Homme Intense ấm, mịn và lịch lãm. Iris tạo cảm giác phấn mềm sang trọng, nền gỗ và hổ phách giúp mùi hương nam tính hơn.',
    longevity = '8 - 12 giờ',
    occasion = 'Hẹn hò tối, mùa lạnh, tiệc sang trọng'
WHERE slug = 'dior-homme-intense';

UPDATE products SET
    description = 'Mùi hương unisex tinh tế với hoa hồng, hoắc hương và gỗ.',
    top_note = 'Cam bergamot, hoa hồng, hương xanh',
    middle_note = 'Hoắc hương, hoa nhài, iris',
    base_note = 'Rêu sồi, gỗ đàn hương, hổ phách',
    fragrance_story = 'Gris Dior là mùi hương unisex tinh tế, có nét hoa hồng khô, gỗ và chypre thanh lịch. Mùi hương không quá ngọt, hợp với phong cách tối giản.',
    longevity = '7 - 9 giờ',
    occasion = 'Đi làm, tiệc nhẹ, gặp gỡ quan trọng'
WHERE slug = 'dior-gris-dior';

UPDATE products SET
    description = 'Hương da thuộc, gỗ và hoa violet tạo cảm giác mạnh mẽ.',
    top_note = 'Quýt mandarin, hoa violet, cam bergamot',
    middle_note = 'Nhục đậu khấu, hoa violet, đinh hương',
    base_note = 'Da thuộc, gỗ tuyết tùng, cỏ vetiver',
    fragrance_story = 'Fahrenheit mạnh mẽ và khác biệt với nét da thuộc, violet và gỗ khô. Mùi hương tạo cảm giác bản lĩnh, có chiều sâu và rất nam tính.',
    longevity = '7 - 10 giờ',
    occasion = 'Đi tối, mùa lạnh, phong cách cá tính'
WHERE slug = 'dior-fahrenheit';

UPDATE products SET
    description = 'Hương hoa hồng và mẫu đơn nhẹ nhàng, trẻ trung.',
    top_note = 'Quýt Sicily, dâu hồng, quả mọng đỏ',
    middle_note = 'Hoa mẫu đơn, hoa hồng damascena',
    base_note = 'Xạ hương trắng, hương gỗ mềm',
    fragrance_story = 'Blooming Bouquet trong trẻo, trẻ trung và rất dễ dùng. Mùi hoa hồng mẫu đơn tạo cảm giác dịu dàng, hợp với những ngày cần sự nhẹ nhàng.',
    longevity = '4 - 6 giờ',
    occasion = 'Đi học, đi làm, cà phê, ban ngày'
WHERE slug = 'miss-dior-blooming-bouquet';

UPDATE products SET
    description = 'Mùi hương cà phê, vani và hoa trắng quyến rũ, hiện đại.',
    top_note = 'Lê, tiêu hồng, hoa cam',
    middle_note = 'Cà phê đen, hoa nhài, cam thảo',
    base_note = 'Vani, tuyết tùng, hoắc hương',
    fragrance_story = 'Black Opium ngọt ấm, quyến rũ và hiện đại. Nét cà phê tạo điểm nhấn cá tính, vani và hoa trắng làm mùi hương mềm và gợi cảm.',
    longevity = '7 - 10 giờ',
    occasion = 'Hẹn hò tối, tiệc, mùa lạnh'
WHERE slug = 'ysl-black-opium';

UPDATE products SET
    description = 'Hương nam tươi mát với táo xanh, xô thơm và gỗ ấm.',
    top_note = 'Táo xanh, gừng, cam bergamot',
    middle_note = 'Xô thơm, phong lữ, quả bách xù',
    base_note = 'Gỗ tuyết tùng, hổ phách, đậu tonka',
    fragrance_story = 'Y EDP tươi mát nhưng có độ ấm và nam tính. Táo xanh tạo sự trẻ trung, xô thơm và gỗ giúp mùi hương gọn gàng, hiện đại.',
    longevity = '7 - 9 giờ',
    occasion = 'Đi làm, đi chơi, gặp bạn, hẹn hò'
WHERE slug = 'ysl-y-eau-de-parfum';

UPDATE products SET
    description = 'Mùi hương cay ấm, lịch lãm với bạch đậu khấu và lavender.',
    top_note = 'Bạch đậu khấu, cam bergamot',
    middle_note = 'Lavender, tuyết tùng, caraway',
    base_note = 'Cỏ vetiver, coumarin, gỗ ấm',
    fragrance_story = 'La Nuit de L Homme ấm áp, gần gũi và lịch lãm. Mùi hương cay nhẹ, mềm và cuốn hút, phù hợp với không gian gần.',
    longevity = '5 - 7 giờ',
    occasion = 'Hẹn hò, đi tối, mùa mát'
WHERE slug = 'ysl-la-nuit-de-l-homme';

UPDATE products SET
    description = 'Hương trái cây ngọt ngào với dâu tây, lê và hoắc hương.',
    top_note = 'Dâu tây, lê, cam bergamot',
    middle_note = 'Hoa mẫu đơn, hoa nhài, datura',
    base_note = 'Hoắc hương, xạ hương trắng, vani',
    fragrance_story = 'Mon Paris ngọt ngào, trẻ trung và lãng mạn. Trái cây đỏ tạo cảm giác vui tươi, hoắc hương giúp mùi hương có độ bám và quyến rũ.',
    longevity = '6 - 8 giờ',
    occasion = 'Hẹn hò, đi chơi, tiệc nhẹ'
WHERE slug = 'ysl-mon-paris';

UPDATE products SET
    description = 'Mùi hương lavender, hoa cam và vani ấm áp, cá tính.',
    top_note = 'Lavender, quýt mandarin, cam bergamot',
    middle_note = 'Hoa cam, hoa lan, nhài sambac',
    base_note = 'Vani, đậu tonka, hổ phách',
    fragrance_story = 'Libre Intense dày hơn và ấm hơn bản gốc. Lavender vẫn cá tính, nhưng vani và hổ phách làm mùi hương trở nên sâu, mềm và quyến rũ.',
    longevity = '8 - 11 giờ',
    occasion = 'Đi tối, dự tiệc, mùa lạnh'
WHERE slug = 'ysl-libre-intense';

UPDATE products SET
    description = 'Hương hoa dành dành, lê và đường nâu ngọt dịu.',
    top_note = 'Hoa lê, trái cây đỏ, đường nâu',
    middle_note = 'Hoa gardenia, hoa nhài',
    base_note = 'Hoắc hương, gỗ trắng, xạ hương',
    fragrance_story = 'Flora Gorgeous Gardenia ngọt dịu, tươi vui và nữ tính. Mùi hoa gardenia được làm mềm bằng trái cây, phù hợp phong cách trẻ trung.',
    longevity = '5 - 7 giờ',
    occasion = 'Đi chơi, ban ngày, hẹn hò nhẹ'
WHERE slug = 'gucci-flora-gorgeous-gardenia';

UPDATE products SET
    description = 'Mùi hương hoa cổ điển pha chút hiện đại và quyến rũ.',
    top_note = 'Tiêu hồng, cam bergamot, quýt mandarin',
    middle_note = 'Hoa tử đinh hương, hoa phong lữ, đào',
    base_note = 'Hoắc hương, hổ phách, xạ hương trắng',
    fragrance_story = 'Gucci Guilty Pour Femme có nét hoa ấm, quyến rũ và hiện đại. Mùi hương không quá nặng, dễ dùng khi muốn tạo ấn tượng thanh lịch.',
    longevity = '6 - 8 giờ',
    occasion = 'Đi làm, hẹn hò, đi tối'
WHERE slug = 'gucci-guilty-pour-femme';

UPDATE products SET
    description = 'Hương lavender, chanh và gỗ tuyết tùng nam tính.',
    top_note = 'Chanh, lavender',
    middle_note = 'Hoa cam, tiêu hồng',
    base_note = 'Gỗ tuyết tùng, hoắc hương, xạ hương',
    fragrance_story = 'Gucci Guilty Pour Homme tươi mát, cay nhẹ và nam tính. Lavender làm mùi hương sạch, nền gỗ giúp tổng thể lịch lãm hơn.',
    longevity = '5 - 7 giờ',
    occasion = 'Đi làm, hằng ngày, gặp gỡ bạn bè'
WHERE slug = 'gucci-guilty-pour-homme';

UPDATE products SET
    description = 'Mùi hương unisex với hoa cúc La Mã, nhài và gỗ đàn hương.',
    top_note = 'Hoa cúc La Mã, hạnh nhân đắng',
    middle_note = 'Hoa nhài, xạ hương, hương xanh',
    base_note = 'Gỗ đàn hương, tuyết tùng, vani nhẹ',
    fragrance_story = 'Memoire d une Odeur có phong cách unisex lạ, xanh và trầm. Mùi hương nhẹ như ký ức, không ồn ào nhưng có cá tính riêng.',
    longevity = '5 - 7 giờ',
    occasion = 'Hằng ngày, phong cách tối giản, đi làm'
WHERE slug = 'gucci-memoire-d-une-odeur';

UPDATE products SET
    description = 'Bộ quà tặng nước hoa Gucci Flora kèm sản phẩm chăm sóc cơ thể.',
    top_note = 'Lê, cam chanh, trái cây đỏ',
    middle_note = 'Hoa gardenia, hoa nhài, hoa trắng',
    base_note = 'Đường nâu, hoắc hương, xạ hương trắng',
    fragrance_story = 'Gucci Flora Gift Set là lựa chọn quà tặng nữ tính và sang. Hương hoa trái cây ngọt dịu, dễ dùng và dễ tạo thiện cảm.',
    longevity = '5 - 7 giờ',
    occasion = 'Quà tặng, sinh nhật, hẹn hò, đi chơi'
WHERE slug = 'gucci-flora-gift-set';
INSERT INTO vouchers (
    code, name, discount_type, discount_value, min_order_value,
    max_discount, quantity, start_date, end_date
)
VALUES
('WELCOME10', 'Giảm 10% cho khách mới', 'percent', 10, 1000000, 500000, 100, '2026-01-01 00:00:00', '2026-12-31 23:59:59'),
('FREESHIP50', 'Hỗ trọ phí vận chuyển', 'fixed', 50000, 500000, NULL, 200, '2026-01-01 00:00:00', '2026-12-31 23:59:59');

INSERT INTO promotions (name, description, discount_percent, start_date, end_date)
VALUES
('Summer Perfume Sale', 'Khuyến mãi mùa hè cho sản phẩm nổi bật', 8, '2026-06-01 00:00:00', '2026-06-30 23:59:59');

INSERT INTO product_promotions (product_id, promotion_id)
VALUES
(1, 1),
(3, 1),
(5, 1);

INSERT INTO carts (user_id) VALUES (3);

INSERT INTO cart_items (cart_id, product_id, quantity)
VALUES
(1, 1, 1),
(1, 3, 2);

INSERT INTO orders (
    user_id, voucher_id, order_code, receiver_name, receiver_phone,
    receiver_email, receiver_address, total_amount, discount_amount,
    shipping_fee, final_amount, payment_method, payment_status, status
)
VALUES
(3, 1, 'OD202606080001', 'Khach Hang Demo', '0900000003', 'user@example.com', 'Quan 1, TP Ho Chi Minh', 4200000, 420000, 30000, 3810000, 'cod', 'unpaid', 'confirmed');

INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, subtotal)
VALUES
(1, 1, 'Chanel N5 Eau de Parfum', '/public/upload/N5DEPARFUM.webp', 4200000, 1, 4200000);

INSERT INTO payments (order_id, amount, method, status)
VALUES
(1, 3810000, 'cod', 'unpaid');

INSERT INTO order_status_logs (order_id, status, note, changed_by)
VALUES
(1, 'pending', 'Khach hang vua dat hang', 3),
(1, 'confirmed', 'Nhan vien da xac nhan don hang', 2);

INSERT INTO reviews (user_id, product_id, order_id, rating, comment, status)
VALUES
(3, 1, 1, 5, 'Mùi hương sang trọng, lưu hương tốt.', 'approved');

INSERT INTO notifications (title, content, type, sent_by)
VALUES
('Xác nhận đơn hàng', 'Đơn hàng OD202606080001 của bạn đã được xác nhận.', 'email', 2);

INSERT INTO user_notifications (notification_id, user_id)
VALUES
(1, 3);

-- Truy van mau: doanh thu theo tuan
-- SELECT YEAR(created_at) AS year, WEEK(created_at, 1) AS week, SUM(final_amount) AS revenue
-- FROM orders
-- WHERE status = 'completed'
-- GROUP BY YEAR(created_at), WEEK(created_at, 1);

-- Truy van mau: doanh thu theo thang
-- SELECT YEAR(created_at) AS year, MONTH(created_at) AS month, SUM(final_amount) AS revenue
-- FROM orders
-- WHERE status = 'completed'
-- GROUP BY YEAR(created_at), MONTH(created_at);

-- Truy van mau: san pham ban chay
-- SELECT product_id, product_name, SUM(quantity) AS total_sold
-- FROM order_items
-- GROUP BY product_id, product_name
-- ORDER BY total_sold DESC;

-- Truy van mau: san pham ban e
-- SELECT p.id, p.name, COALESCE(SUM(oi.quantity), 0) AS total_sold
-- FROM products p
-- LEFT JOIN order_items oi ON p.id = oi.product_id
-- GROUP BY p.id, p.name
-- ORDER BY total_sold ASC;

-- =========================================================
-- FIX ẢNH SẢN PHẨM THEO ĐÚNG FILE TRONG public/upload
-- Đường dẫn đang dùng: http://localhost/public/upload/<ten-file>
-- =========================================================
UPDATE products SET main_image = '/public/upload/N5DEPARFUM.webp' WHERE slug = 'chanel-n5-eau-de-parfum';
UPDATE products SET main_image = '/public/upload/N5DETOILETTE.webp' WHERE slug = 'chanel-n5-eau-de-toilette';
UPDATE products SET main_image = '/public/upload/N5LEAU.webp' WHERE slug = 'chanel-n5-leau';
UPDATE products SET main_image = '/public/upload/Coco Mademoiselle.avif' WHERE slug = 'coco-mademoiselle';
UPDATE products SET main_image = '/public/upload/chanel-chance-eau-tendre.webp' WHERE slug = 'chanel-chance-eau-tendre';
UPDATE products SET main_image = '/public/upload/Chanel Gabrielle Essence.webp' WHERE slug = 'chanel-gabrielle-essence';
UPDATE products SET main_image = '/public/upload/Chanel Paris-Biarritz.avif' WHERE slug = 'chanel-paris-biarritz';
UPDATE products SET main_image = '/public/upload/Allure Homme Sport.jpg' WHERE slug = 'allure-homme-sport';
UPDATE products SET main_image = '/public/upload/bleu-de-chanel-parfum.webp' WHERE slug = 'bleu-de-chanel-parfum';
UPDATE products SET main_image = '/public/upload/dior-sauvage.webp' WHERE slug = 'dior-sauvage';
UPDATE products SET main_image = '/public/upload/dior-homme-intense.webp' WHERE slug = 'dior-homme-intense';
UPDATE products SET main_image = '/public/upload/Dior Fahrenheit.jpg' WHERE slug = 'dior-fahrenheit';
UPDATE products SET main_image = '/public/upload/Dior Gris Dior.jpg' WHERE slug = 'dior-gris-dior';
UPDATE products SET main_image = '/public/upload/Dior J adore Eau de Parfum.jpg' WHERE slug = 'dior-jadore-eau-de-parfum';
UPDATE products SET main_image = '/public/upload/Miss Dior Blooming Bouquet.jpg' WHERE slug = 'miss-dior-blooming-bouquet';
UPDATE products SET main_image = '/public/upload/ysl-libre.webp' WHERE slug = 'ysl-libre';
UPDATE products SET main_image = '/public/upload/ysl-libre-intense.webp' WHERE slug = 'ysl-libre-intense';
UPDATE products SET main_image = '/public/upload/ysl-mon-paris.webp' WHERE slug = 'ysl-mon-paris';
UPDATE products SET main_image = '/public/upload/ysl-black-opium.webp' WHERE slug = 'ysl-black-opium';
UPDATE products SET main_image = '/public/upload/ysl-la-nuit-de-l-homme.webp' WHERE slug = 'ysl-la-nuit-de-l-homme';
UPDATE products SET main_image = '/public/upload/ysl-y-eau-de-parfum.webp' WHERE slug = 'ysl-y-eau-de-parfum';
UPDATE products SET main_image = '/public/upload/gucci-bloom-gift-set.webp' WHERE slug = 'gucci-bloom-gift-set';
UPDATE products SET main_image = '/public/upload/gucci-flora-gift-set.webp' WHERE slug = 'gucci-flora-gift-set';
UPDATE products SET main_image = '/public/upload/gucci-flora-gorgeous-gardenia.webp' WHERE slug = 'gucci-flora-gorgeous-gardenia';
UPDATE products SET main_image = '/public/upload/gucci-guilty-pour-femme.webp' WHERE slug = 'gucci-guilty-pour-femme';
UPDATE products SET main_image = '/public/upload/gucci-guilty-pour-homme.webp' WHERE slug = 'gucci-guilty-pour-homme';
UPDATE products SET main_image = '/public/upload/gucci-memoire-d-une-odeur.webp' WHERE slug = 'gucci-memoire-d-une-odeur';

DELETE FROM product_images;

INSERT INTO product_images (product_id, image_path, is_main)
SELECT id, main_image, 1
FROM products
WHERE main_image IS NOT NULL AND main_image <> '';





-- InfinityFree final fixes
UPDATE products SET main_image = REPLACE(main_image, '/KV_Perfume-main/public/upload/', '/public/upload/');
UPDATE products SET main_image = REPLACE(main_image, '/CHANEL.VN-MAIN/public/upload/', '/public/upload/');
UPDATE product_images SET image_path = REPLACE(image_path, '/KV_Perfume-main/public/upload/', '/public/upload/');
UPDATE product_images SET image_path = REPLACE(image_path, '/CHANEL.VN-MAIN/public/upload/', '/public/upload/');
UPDATE order_items SET product_image = REPLACE(product_image, '/KV_Perfume-main/public/upload/', '/public/upload/');
UPDATE order_items SET product_image = REPLACE(product_image, '/CHANEL.VN-MAIN/public/upload/', '/public/upload/');
SET FOREIGN_KEY_CHECKS = 1;
