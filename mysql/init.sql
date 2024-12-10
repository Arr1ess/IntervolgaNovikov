CREATE TABLE `clients` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT 'unknown',
    `password` VARCHAR(255),
    `email` VARCHAR(255),
    PRIMARY KEY(`id`),
    UNIQUE KEY `email_unique` (`email`)
);

CREATE TABLE `products` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255),
    `price` DECIMAL(10, 2),
    PRIMARY KEY(`id`)
);

CREATE TABLE `dishes` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255),
    `image_url` VARCHAR(255),
    `weight` DOUBLE,
    `compound` VARCHAR(255),
    `menu_id` INTEGER,
    PRIMARY KEY(`id`)
);

CREATE TABLE `menus` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255),
    `type` ENUM('regular', 'banquet', 'business lunch'),
    PRIMARY KEY(`id`)
);

CREATE TABLE `orders` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `client_id` INTEGER,
    `date` DATE,
    PRIMARY KEY(`id`)
);

CREATE TABLE `dishes_to_order` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `order_id` INTEGER,
    `dish_id` INTEGER,
    PRIMARY KEY(`id`)
);

CREATE TABLE `products_to_dish` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `product_id` INTEGER,
    `dish_id` INTEGER,
    PRIMARY KEY(`id`)
);

ALTER TABLE `orders`
ADD FOREIGN KEY(`client_id`) REFERENCES `clients`(`id`)
ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE `dishes`
ADD FOREIGN KEY(`menu_id`) REFERENCES `menus`(`id`)
ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `dishes_to_order`
ADD FOREIGN KEY(`dish_id`) REFERENCES `dishes`(`id`)
ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE `dishes_to_order`
ADD FOREIGN KEY(`order_id`) REFERENCES `orders`(`id`)
ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `products_to_dish`
ADD FOREIGN KEY(`dish_id`) REFERENCES `dishes`(`id`)
ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `products_to_dish`
ADD FOREIGN KEY(`product_id`) REFERENCES `products`(`id`)
ON UPDATE CASCADE ON DELETE CASCADE;