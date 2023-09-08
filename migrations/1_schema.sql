SET FOREIGN_KEY_CHECKS=0;

# -----------------------------------------------
# Banners ---------------------------------------
# -----------------------------------------------

DROP TABLE IF EXISTS app_banner;

CREATE TABLE app_banner (
  banner_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  banner_name VARCHAR(32) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  banner_image VARCHAR(64) DEFAULT NULL COMMENT 'The name of the image from the images folder',
  PRIMARY KEY(banner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

DROP TABLE IF EXISTS app_banner_stats;

CREATE TABLE app_banner_stats (
  hash_id VARCHAR(32) NOT NULL,
  banner_id SMALLINT UNSIGNED NOT NULL,
  ip_address INT(11) UNSIGNED NOT NULL,
  user_agent TEXT COLLATE utf8mb3_unicode_ci NOT NULL,
  page_url VARCHAR(2048) NOT NULL,
  views_count INT(11) UNSIGNED NOT NULL,
  view_date DATETIME DEFAULT NULL,
  PRIMARY KEY(hash_id),
  FOREIGN KEY(banner_id) REFERENCES app_banner(banner_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

# -----------------------------------------------

SET FOREIGN_KEY_CHECKS=1;
