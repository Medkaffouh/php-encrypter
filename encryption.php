<?php 

/**
 * src : source folder 
 * encrypted : Output folder
 */

$src      = 'src';


/**
 * No need to edit following code 
 */

$excludes = array('vendor');

foreach($excludes as $key => $file){
    $excludes[ $key ] = $src.'/'.$file;
}

$rec = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $src ));
$require_funcs = array('include_once', 'include', 'require', 'require_once'); 


foreach ($rec as $file) {

    if ($file->isDir()) {
        $newDir  = str_replace( 'src', 'encrypted', $file->getPath() );
        if( !is_dir( $newDir ) ) mkdir( $newDir );
        continue;
    };

    $filePath = $file->getPathname();

    if( pathinfo($filePath, PATHINFO_EXTENSION) != 'php'  ||
        in_array( $filePath, $excludes ) ) {  
        $newFile  = str_replace('src', 'encrypted', $filePath );
        copy( $filePath, $newFile );
        continue;
    }

    $contents = file_get_contents( $filePath );
    
    $re = '/\<\?php/m';
    preg_match($re, $contents, $matches ); 
    if(!empty($matches[0]) ){
        $contents = preg_replace( $re, '', $contents );
        ##!!!##';
    }

    //$e = base64_encode($contents);
    $cipher = openssl_encrypt("$contents","AES-256-CBC","medkaffouh123456",0,"1234567812345678");
    $preppand = '<?php 
        eval(openssl_decrypt("'.$cipher.'","AES-256-CBC","medkaffouh123456",0,"1234567812345678"));';

    $newFile  = str_replace('src', 'encrypted', $filePath );
    $fp = fopen( $newFile, 'w');
    fwrite($fp, $preppand);
    fclose($fp);

    unset( $cipher );
    unset( $contents );
}

$out_str       = substr_replace($src, '', 0, 4);
$file_location = __DIR__."/encrypted/".$out_str;
echo "Successfully Encrypted... Please check in <b>" .$file_location."</a></b> folder.";
