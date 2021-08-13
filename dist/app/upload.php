<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
$g_uploaddir = '/Applications/MAMP/htdocs/file-upload/dist/uploads';
$g_uploadfile = sprintf("%s/%s",$g_uploaddir, basename($_FILES['upl']['name']));
//echo $g_uploadfile;
#header('Content-Type: text/plain; charset=utf-8');
//echo $the_uploaded =process_uploadfile();
//perform_python($the_uploaded);
//$the_uploaded = dirname('/Applications/MAMP/htdocs/file-upload/dist/app/uploads/file-upload.zip');
//$the_uploaded = '../uploads/file-upload.zip';
process_uploadfile();
perform_python($g_uploadfile);


function perform_python($file) {
    global $g_uploaddir;
    $realpath= realpath($file);
    //echo $realpath;
    $oky=null;
    $out= null;
    exec( sprintf("tar zxf %s --directory %s",$realpath, "../uploads/"), $out, $oky);
    /* if ($oky !=0) {
        var_dump($out);
        throw new RuntimeException($out);
    } */
    
    //$py_filename = sprintf("%s", preg_replace('/\\.[^.\\s]{3,4}$/', '', $realpath) );
    //echo $py_filename;
    //print_r( PATHINFO_EXTENSION);
    //$ext = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
    //$py_filename = pathinfo($_FILES['upl']['name'], PATHINFO_FILENAME);
    $py_filename = pathinfo($realpath, PATHINFO_FILENAME);
    $py_fullpath = $g_uploaddir."/".$py_filename;
    if ( is_file( $py_fullpath ) )
    {
        //exec( "python ".$py_filename , $out, $oky);
        //echo $py_fullpath;
        exec( sprintf("python %s 2>&1",$py_fullpath) , $out, $oky);
        echo "[Return Code=".$oky."]";        
        
    } elseif ( file_exists ( $py_fullpath."py"))
    {
        exec( sprintf("python %s 2>&1",$py_fullpath) , $out, $oky);
        echo "[Return Code=".$oky."]";
 
    } elseif (is_dir($py_filename) ) {
        echo ("folder");
        exec( sprintf("python %s.%s 2>&1",$py_filename, pathinfo($_FILES['upl']['name'], PATHINFO_FILENAME) ), $out, $oky);
        echo "[Return Code=".$oky."]";      
        
    } else {
        
        echo "Unhandled Exception";
    }
    
    if ($oky !=0) {
        print_r($out);
        throw new RuntimeException("Perform python error code: ".$oky);
    }
    
    
}


function process_uploadfile(){
    //$uploaddir = '/Applications/MAMP/htdocs/file-upload/dist/uploads/';
    //$uploadfile = $uploaddir . basename($_FILES['upl']['name']);
    #echo strval(is_uploaded_file(basename($_FILES['upl']['name'])));
    global $g_uploadfile;
    //echo strval($g_uploadfile.'\n');
    //echo gettype( $_FILES['upl']['tmp_name']).'\n';
    
    //$allowedCompressedTypes = array("application/x-rar-compressed", "application/zip", "application/x-zip", "application/octet-stream", "application/x-zip-compressed");
    
    try {
        /* if (! in_array($_FILES["upl"]["type"], $allowedCompressedTypes) ) {
            throw new RuntimeException('Only .zip file is accepted.');
        }
        if (! isRarOrZip(basename($_FILES['upl']) )) {
            throw new RuntimeException('Only .zip file is accepted.');
        } */
        
        //$tmpFiles = incoming_files();
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['upl']['error']) ||
            is_array($_FILES['upl']['error'])
        ) {
            throw new RuntimeException('Invalid parameters in upload files.');
        }
    
        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upl']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
    
        // You should also check filesize here.
        if ($_FILES['upl']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
    
        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        //$allowedCompressedTypes = array("application/x-rar-compressed", "application/zip", "application/x-zip", "application/octet-stream", "application/x-zip-compressed");
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['upl']['tmp_name']),
            array(
                'rar' => 'application/x-rar-compressed',
                'zip' => 'application/zip',
                'gz' =>'application/x-gzip'
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }
    
        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        //$uploaded_file = sprintf('../uploads/%s',  basename($_FILES['upl']['name']));
        if (!move_uploaded_file(
            $_FILES['upl']['tmp_name'],
            //sprintf('../uploads/%s.%s',  sha1_file($_FILES['upl']['tmp_name']), $ext)
            sprintf('../uploads/%s',  basename($_FILES['upl']['name']))
        )) {
            throw new RuntimeException('Failed to save uploaded file.');
        }
    
        echo 'File is uploaded successfully.\n';
        //$uploaded_file = dirname($uploaded_file);
        //echo $uploaded_file.'\n';
        //return $g_uploadfile;
    } catch (RuntimeException $e) {
    
        echo $e->getMessage();
    
    }
}

function isRarOrZip($file) {
    // get the first 7 bytes
    $bytes = file_get_contents($file, FALSE, NULL, 0, 7);
    $ext = strtolower(substr($file, - 4));
    
    // RAR magic number: Rar!\x1A\x07\x00
    // http://en.wikipedia.org/wiki/RAR
    if ($ext == '.rar' and bin2hex($bytes) == '526172211a0700') {
        return TRUE;
    }
    
    // ZIP magic number: none, though PK\003\004, PK\005\006 (empty archive),
    // or PK\007\008 (spanned archive) are common.
    // http://en.wikipedia.org/wiki/ZIP_(file_format)
    if ($ext == '.zip' and substr($bytes, 0, 2) == 'PK') {
        return TRUE;
    }
    
    return FALSE;
}

function incoming_files() {
    $files = $_FILES;
    $files2 = [];
    foreach ($files as $input => $infoArr) {
        $filesByInput = [];
        foreach ($infoArr as $key => $valueArr) {
            if (is_array($valueArr)) { // file input "multiple"
                foreach($valueArr as $i=>$value) {
                    $filesByInput[$i][$key] = $value;
                }
            }
            else { // -> string, normal file input
                $filesByInput[] = $infoArr;
                break;
            }
        }
        $files2 = array_merge($files2,$filesByInput);
    }
    $files3 = [];
    foreach($files2 as $file) { // let's filter empty & errors
        if (!$file['error']) $files3[] = $file;
    }
    return $files3;
}



?>