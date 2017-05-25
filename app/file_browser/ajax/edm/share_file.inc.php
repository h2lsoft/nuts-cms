<?php
/**
 * share file
 */

set_time_limit(0);
ini_set('memory_limit', $share_file_memory_limit);

// controller **********************************************************************************************************
$folder = urldecode(@$_POST["folder"]);
$files = (array)@$_POST["files"];
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';

// check path exists and no forbidden
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    $msg = "The folder path was tampered with !";
    edmLog('SHARE', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

if(!count($files))
{
    systemError(translate("Parameters not correct !"));
}


// right access verification
if(!edmUserHasRight('SHARE', $folder))
{
    $msg = "Action not allowed !";
    edmLog('SHARE', 'ERROR', $folder, $msg);
    systemError(translate($msg));
}

// verify form data
$_POST['recipient'] = trim(@$_POST['recipient']);
if(empty($_POST['recipient']))
{
    $not_empty = ($_SESSION['Language'] == 'fr') ? "Le champ `Destinataire` doit être rempli" : "Field `Recipient` must be filled";
    systemError($not_empty);
}

if(!email($_POST['recipient']))
{
    $not_email = ($_SESSION['Language'] == 'fr') ? "Le champ `Destinataire` doit être une adresse email valide" : "Field `Recipient` must be a valid email address";
    systemError($not_email);
}

$_POST['subject'] = trim(@$_POST['subject']);
if(empty($_POST['subject']))
{
    $not_empty = ($_SESSION['Language'] == 'fr') ? "Le champ `Sujet` doit être rempli" : "Field `Subject` must be filled";
    systemError($not_empty);
}

$_POST['message'] = trim(@$_POST['message']);
if(strpos($_POST['message'], '[FILE_URL]') === false)
{
    $msg = ($_SESSION['Language'] == 'fr') ? "Le champ `Message` doit contenir le mot clef `[FILE_URL]`" : "Field `Message` must be contained keyword `[FILE_URL]`";
    systemError($msg);
}

// expiration
$_POST['expiration'] = (int)@$_POST['expiration'];
if($_POST['expiration'] <= 0)
{
    $msg = ($_SESSION['Language'] == 'fr') ? "Le champ `Expiration` doit contenir un nombre valide" : "Field `Expiration` must be contained a valid number";
    systemError($msg);
}

if($_POST['expiration'] > $share_file_max_expiration)
{
    $msg = ($_SESSION['Language'] == 'fr') ? "Le champ `Expiration` ne peut pas être supérieur à $share_file_max_expiration jours" : "Field `Expiration` can not be greater $share_file_max_expiration days";
    systemError($msg);
}



$_POST['acknowledgment'] = (int)@$_POST['acknowledgment'];
if($_POST['acknowledgment'] != 1 && $_POST['acknowledgment'] != 0)
    $_POST['acknowledgment'] = 0;


// zip name
$_POST['zip_name'] = trim(@$_POST['zip_name']);
$tmp = str_replace(array('_', '-'), '', $_POST['zip_name']);
$tmp = trim($tmp);

if(empty($tmp))
{
    $not_empty = ($_SESSION['Language'] == 'fr') ? "Le champ `Nom de fichier` doit être rempli" : "Field `File name` must be filled";
    systemError($not_empty);
}

if(!ctype_alnum($tmp))
{
    $msg = ($_SESSION['Language'] == 'fr') ? "Le champ `Nom de fichier` doit être alphanumérique" : "Field `Recipient` must be alphanumeric";
    systemError($msg);
}


// verify extension allowed
$total_size = 0;
foreach($files as $file)
{
    $file = urldecode($file);
    $ext = strtolower(end(explode('.', basename($file))));
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;
    if((!$is_dir && !is_file(WEBSITE_PATH.$file)) || ($is_dir && !is_dir(WEBSITE_PATH.$file)) || !preg_match("#^$upload_pathX#", $file) || (!$is_dir && !in_array($ext, $filetypes_exts)))
    {
        $type = ($is_dir) ? 'FOLDER' : 'FILE';
        $msg = "Parameters files `$file` not correct !";
        edmLog('SHARE', 'ERROR', $type, $msg);
        systemError(translate($msg));
    }

    // user has right for file or folder to cut it ?
    if($is_dir)
    {
        if(!edmUserHasRight('SHARE', $file))
        {
            $msg = "Action not allowed for folder `$file`";
            edmLog('SHARE', 'ERROR', $file, $msg);
            systemError(translate($msg));
        }

        // check folder lock
        edmCheckLock($file, "", 'json');

        $total_size += filesize(WEBSITE_PATH.$file);
    }
    else
    {
        $cur_folder = str_replace(basename($file), '', $file);
        if(!edmUserHasRight('SHARE', $cur_folder))
        {
            $msg = "Action not allowed for folder `$cur_folder`";
            edmLog('SHARE', 'ERROR', $cur_folder, $msg);
            systemError(translate($msg));
        }

        // check file lock
        edmCheckLock($cur_folder, basename($file), 'json');
        $total_size += filesize(WEBSITE_PATH.$file);

    }
}


// verify total zip
if($total_size > $share_file_max_zip_size)
{
    $share_file_mo = $share_file_max_zip_size / 1024 / 1024;
    $share_file_mo = round($share_file_mo, 2);
    $share_file_mo = number_formatX($share_file_mo);

    $total_size_mo = $total_size / 1024 / 1024;
    $total_size_mo = round($total_size_mo, 2);
    $total_size_mo = number_formatX($total_size_mo);

    $msg = ($_SESSION['Language'] == 'fr') ? "Votre fichier dépasse la limite autorisée de {$share_file_mo}Mo ($total_size_mo  Mo)" : "Your zip size is greater than maximum size allowed {$share_file_mo}Mo  ($total_size_mo Mo)";
    systemError($msg);
}

$correct_files = array();
foreach($files as $file)
{
    $file = urldecode($file);
    if(!empty($file))$correct_files[] = $file;
}



// zip process in folder exchange
$zip_path = NUTS_PLUGINS_PATH.'/_edm/exchange';
$zip_token = uniqid('ZIP', true);

$f = array();
$f['NutsUserID'] = $_SESSION['NutsUserID'];
$f['DateCreate'] = 'NOW()';
$f['Token'] = $zip_token;
$f['`To`'] = $_POST['recipient'];
$f['AR'] = $_POST['acknowledgment'];
$f['Expiration'] = $_POST['expiration'];
$f['Subject'] = $_POST['subject'];
$f['Message'] = $_POST['message'];
$f['Files'] = join("\n", $correct_files);
$f['ZipName'] =  $_POST['zip_name'];
$ZIP_ID = $nuts->dbInsert('NutsEDMShare', $f, array(), true);

$zip_file = $zip_path.'/'.$ZIP_ID.'.zip';



// zip creation
$zip = new ZipArchive();
if(@$zip->open($zip_file, ZIPARCHIVE::OVERWRITE) === false)
{
    $msg = "Error: while zip creation";

    // trigger
    nutsTrigger('edm::share_system_zip_error', true, "edm user action share files / folder");
    edmLog('SHARE', 'ERROR', $folder, $msg);

    systemError($msg);
}

foreach($correct_files as $file)
{
    $ext = strtolower(end(explode('.', basename($file))));
    $is_dir = ($file[strlen($file)-1] == '/') ? true : false;

    if($is_dir)
    {
        $folder_name = basename($file);
        $options = array('remove_path' => WEBSITE_PATH.$folder, 'add_path' => $folder_name[0]);
        $zip->addGlob(WEBSITE_PATH.$file.'*.*', 0, $options);

        $subdirs = glob_recursiveX(WEBSITE_PATH.$file); # complete path
        foreach($subdirs as $subdir)
        {
            $options = array('remove_path' => WEBSITE_PATH.$folder, 'add_path' => $folder_name[0]);
            $zip->addGlob($subdir.'/*.*', 0, $options);
        }
    }
    else
    {
        $zip->addFile(WEBSITE_PATH.$file, basename($file));
    }
}

$zip->close();

// send email formated
$f = array();
$f['subject'] = $_POST['subject'];
$file_url = WEBSITE_URL.'/plugins/_edm/exchange.php?token='.$zip_token.'&email='.$_POST['recipient'];
$f['body'] = str_replace('[FILE_URL]', $file_url, $_POST['message']);
nutsSendEmail($f, array(), $_POST['recipient'], $share_file_mail_add_signature, $_SESSION['Email'], $share_file_mail_add_webapp_subject);


// success
nutsTrigger('edm::share_success', true, "edm user action share files / folder success");
$resp['message'] = ($_SESSION['Language'] == 'fr') ? "Votre fichier a bien été envoyé" : "Your file has been successfully created";
edmLog('SHARE', 'SUCCESS', $folder, $resp['message'], join("\n", $correct_files));


