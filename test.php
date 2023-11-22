<?php

        $phpVersion = phpversion();
        $data['phpOk'] = version_compare($phpVersion, '7.1.30') >= 0;
        $data['ctype'] = extension_loaded('ctype');
        $data['iconv'] = extension_loaded('iconv');
        $data['json'] = extension_loaded('json');
        $data['pcre'] = extension_loaded('pcre');
        $data['session'] = extension_loaded('session');
        $data['SimpleXML'] = extension_loaded('SimpleXML');
        $data['tokenizer'] = extension_loaded('tokenizer');
        $data['intl'] = extension_loaded('intl');
        $data['openssl'] = extension_loaded('openssl');
        $data['dom'] = extension_loaded('dom');
        $data['mbstring'] = extension_loaded('mbstring');
        $data['curl'] = extension_loaded('curl');
        $data['fileinfo'] = extension_loaded('fileinfo');
        $data['gd'] = extension_loaded('gd');
        $data['libxml'] = extension_loaded('libxml');
        $data['xml'] = extension_loaded('xml');
        $data['xmlreader'] = extension_loaded('xmlreader');
        $data['xmlwriter'] = extension_loaded('xmlwriter');
        $data['zip'] = extension_loaded('zip');
        $data['zlib'] = extension_loaded('zlib');
        
        
        echo('<pre>');
        print_r($data); 
        echo('</pre>');
        
        
        
        $folder['cacheFolderWritable'] = is_writable('var/cache/');
        $folder['logFolderWritable'] = is_writable('var/log/');
        $folder['mediaFolderWritable'] = is_writable('public/media/');
        $folder['uploadsFolderWritable'] = is_writable('public/uploads/');
        $folder['sessionsFolderWritable'] = is_writable('sessions/');
        $folder['jsTranslationsFolderWritable'] = is_writable('assets/js/translations/');
        $folder['envWritable'] = is_writable('.env');
        
        echo('<pre>');
        print_r($folder);
        echo('</pre>');

?>