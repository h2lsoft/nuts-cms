<?php


$data = array();
$data['error'] = false;
$data['error_msg'] = '';

// action verification
if(!@in_array($_GET['_action2'], array('list', 'add', 'delete', 'select')))
{
    $data['error'] = true;
    $data['error_msg'] = 'action not correct';
    die(json_encode($data));
}

// plugin verification
if(!isset($_GET['plugin']))
{
    $data['error'] = true;
    $data['error_msg'] = 'plugin not correct';
    die(json_encode($data));
}

if(!nutsUserHasRight('', $_GET['plugin'], 'list'))
{
    $data['error'] = true;
    $data['error_msg'] = 'plugin not allowed';
    die(json_encode($data));
}

// action list
if($_GET['_action2'] == 'list')
{
    $sql = "SELECT
                    ID, Name
            FROM
                    NutsUserListSearches
            WHERE
                    Deleted = 'NO' AND
                    Plugin = '{$_GET['plugin']}' AND
                    NutsUserID = {$_SESSION['NutsUserID']}
            ORDER BY
                    Name";
    $nuts->doQuery($sql);
    $data['list'] = array();
    while($row = $nuts->dbFetch())
        $data['list'][] = $row;
}

// action add
if($_GET['_action2'] == 'add')
{
    if(@empty($_GET['name']))
    {
        $data['error'] = true;
        $data['error_msg'] = 'search name not correct';
        die(json_encode($data));
    }

    if(@empty($_POST['serialized']))
    {
        $data['error'] = true;
        $data['error_msg'] = 'search serialized not correct';
        die(json_encode($data));
    }

    // save
    $f = array();
    $f['NutsUserID'] = $_SESSION['NutsUserID'];
    $f['Plugin'] = $_GET['plugin'];
    $f['Name'] = ucfirst($nuts->xssProtect(str_replace('"', "`", $_GET['name'])));
    $f['Serialized'] = $_POST['serialized'];

    $nuts->dbInsert('NutsUserListSearches', $f);

}

// action delete
if($_GET['_action2'] == 'delete')
{
    $_GET['ID'] = (int)@$_GET['ID'];
    if(!$_GET['ID'])
    {
        $data['error'] = true;
        $data['error_msg'] = 'ID not correct';
        die(json_encode($data));
    }

    $nuts->dbUpdate('NutsUserListSearches', array('Deleted' => 'YES'), "ID={$_GET['ID']} AND NutsUserID = {$_SESSION['NutsUserID']}");
}

// action select
if($_GET['_action2'] == 'select')
{
    $_GET['ID'] = (int)@$_GET['ID'];
    if(!$_GET['ID'])
    {
        $data['error'] = true;
        $data['error_msg'] = 'ID not correct';
        die(json_encode($data));
    }

    $sql = "SELECT
                    Serialized
            FROM
                    NutsUserListSearches
            WHERE
                    Deleted = 'NO' AND
                    ID = {$_GET['ID']} AND
                    NutsUserID = {$_SESSION['NutsUserID']}";
    $nuts->doQuery($sql);
    $serialized = $nuts->dbGetOne();
    $data['serialized'] = $serialized;
}




die(json_encode($data));
