<?php
    rex_file::copy(rex_path::frontend('.htaccess'), rex_path::frontend('htaccess_backup.txt'));
    rex_file::copy(rex_path::addon('easyurlrewrite', 'var/.htaccess'), rex_path::frontend('.htaccess'));
    rex_delete_cache();
