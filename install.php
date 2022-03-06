<?php
// rex_addon::get('redaxo_url_rewrite') is the alternative among other to $this in install.php
// default: Redaxo CMS is located directly under webroot
rex_config::set($this->getName(), 'redaxo_root', '/');
