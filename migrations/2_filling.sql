# ---------------------------------------------------------------- #
# ---------------------- DEFAULT FILLING-------------------------- #
# ---------------------------------------------------------------- #

SET FOREIGN_KEY_CHECKS=0;

# --

TRUNCATE TABLE app_banner;
TRUNCATE TABLE app_banner_stats;

# --

INSERT INTO app_banner (banner_id, banner_name, banner_image) VALUES
(1, 'Banner #1', 'banner_1.png'),
(2, 'Banner #2', 'banner_2.png');

# --

SET FOREIGN_KEY_CHECKS=1;
