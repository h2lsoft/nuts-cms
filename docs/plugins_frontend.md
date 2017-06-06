# Plugins - Backend

Backend plugins allow you to extend the system, based on user's rights.

Create a simple plugin *"test"* :


* Go to forge plugin forge and make a backend plugin call it *test* with these actions : *"list", "edit", "view", "delete"*
* Download zip file and extract in directory *"/plugins"*
* Go to *"plugin manager"* in adminitration menu and add *"test"* plugin
* Create a table

```sql
CREATE TABLE `MyTest`
(
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `Name` VARCHAR(255) NULL,
    `Email` VARCHAR(255) NULL,
    `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO',
    PRIMARY KEY (`ID`),
    INDEX `Deleted` (`Deleted`)
)
```


* Edit file *"/plugins/test/list.inc.php"*

```php

// assign table to db
$plugin->listSetDbTable('MyTest');

// search engine
$plugin->listSearchAddFieldText('ID');
$plugin->listSearchAddFieldTextAjaxAutoComplete('Name');

// list
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Name', '', '', true);
$plugin->listAddCol('Email', '', '', true);
$plugin->listRender(20, 'hookData');

function hookData($row){
     global $nuts, $plugin;
     
     // hook data before display
     
     return $row;
}
```

* Edit file *"/plugins/test/form.inc.php"*

```php

$plugin->formDBTable(array('MyTest'));

// fields
$plugin->formAddFieldText('Name', '', true);
$plugin->formAddFieldText('Email', '', true, 'lower email');

```


* Edit file *"/plugins/test/view.inc.php"*

```php

$plugin->viewDbTable(array('MyTest'));

$plugin->viewAddVar('Name', '');
$plugin->viewAddVar('Email', '');

$plugin->viewRender();

```


Et voilà !


<aside class="notice">
Don't forget to add rights to user's group in plugin "Right Manager"
</aside>
