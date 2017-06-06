# Componant Query builder

This component helps you to create sql query faster
 
```php
<?php

$logs = Query::factory()->select("FirstName, Action, Resume")
                        ->from('NutsLog, NutsUser')
                        ->whereJoin() # auto join
                        ->order_by("NutsLog.ID DESC")
                        ->limit(5)
                        ->executeAndGetAll();

foreach($logs as $log)
{
    echo "ID: {$log['ID']}<br>";
    echo "Action: {$log['Action']}<br>";
    echo "Resume: {$log['Resume']}<br>";
    echo "<hr>";
} 

``` 