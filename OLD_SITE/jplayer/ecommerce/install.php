<?

	$pluginName 			= "Ecommerce - Online Store";
	$pluginDescription 		= "Ecommerce online store";
	$pluginVersion 			= "1.0";
	$pluginDeveloper 		= "Hi-Fi Media";
	$pluginDeveloperLink 	= "http://www.hi-fimedia.com";
	$pluginActive 			= "1";
	$pluginAdminurl 		= "?fuse=admin.products.manage";
	$pluginDirectory 		= "ecommerce/products/index.php";
	$pluginType 			= "Embed";

	
mq("INSERT INTO `[p]plugins` (`name`, `description`, `version`, `developer`, `developerLink`, `active`, `adminurl`, `directory`, `type`) VALUES
('Ecommerce - Shopping Cart', 'Shopping cart', '1.0', 'Hi-Fi Media', 'http://www.hi-fimedia.com', 1, '?fuse=admin.cart.manage', 'ecommerce/cart/index.php', 'Embed'),
('Ecommerce - Discount Codes', 'Manage and create discount codes for your cart or the entire store.', '2.0', 'Hi-Fi Media', 'http://www.hi-fimedia.com', 1, '?fuse=admin.discount.manage', '', 'Admin')");
	
	
mq("CREATE TABLE IF NOT EXISTS `[p]products` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `origin` varchar(250) NOT NULL,
  `subcat` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `description` longtext NOT NULL,
  `smallDescription` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `price` varchar(64) NOT NULL,
  `discount` varchar(64) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `shipping` varchar(64) NOT NULL,
  `manufacturer` varchar(350) NOT NULL,
  `sale` text NOT NULL,
  `op2` text NOT NULL,
  `op3` text NOT NULL,
  `taxable` varchar(100) NOT NULL,
  `weightlbs` varchar(100) NOT NULL,
  `weightozs` varchar(100) NOT NULL,
  `stock` int(64) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `size` text NOT NULL,
  `color` text NOT NULL,
  PRIMARY KEY (`id`)
)");

mq("CREATE TABLE IF NOT EXISTS `[p]cart` (
  `id` int(24) NOT NULL AUTO_INCREMENT,
  `memberid` int(64) NOT NULL,
  `userid` int(24) NOT NULL,
  `productid` int(24) NOT NULL,
  `price` varchar(300) NOT NULL,
  `name` varchar(300) NOT NULL,
  `email` varchar(250) NOT NULL,
  `person` varchar(350) NOT NULL,
  `street` varchar(350) NOT NULL,
  `city` varchar(150) NOT NULL,
  `state` varchar(150) NOT NULL,
  `zipcode` varchar(100) NOT NULL,
  `country` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(2) NOT NULL DEFAULT '0',
  `code` varchar(200) NOT NULL,
  `shipping` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
)");

mq("CREATE TABLE IF NOT EXISTS `[p]discount` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `percent` varchar(200) NOT NULL,
  `amount` varchar(200) NOT NULL,
  `active` int(3) NOT NULL,
  `expiration` varchar(100) NOT NULL,
  `uses` varchar(100) NOT NULL DEFAULT '0',
  `max` varchar(100) NOT NULL DEFAULT 'unlimited',
  `storewide` int(3) NOT NULL,
  PRIMARY KEY (`id`)
)");
	
	
?>