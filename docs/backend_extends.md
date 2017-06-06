# Extends Backend

# Css

You can add your own css by editing file #"/nuts/css/custom.css"#

# Header

You can add javascript, css or what you want by editing file #"/nuts/_templates/_header_custom.html"#

# Widgets

Widget are displayed in user dashboard, if user has access

3 types of widgets :

* *Full* is 100% of the page
* *2cols* 2 equals columns of 50%
* *3cols* 3 equals columns of 33%

Create a widget is simple :

1. Go to to your plugin folder *"/plugins/my_plugin/"*
1. Create a file *"widget.inc.php"*
1. Edit file and place your content

```php
<?php

Plugin::dashboardAddWidget($widget_title, 'high', 'plugin_name', 'full', 'style', "hello from widget");
```

Et voilà !


# Notification

Notification are displayed in user dashboard.

Create a notification is simple :

1. Go to to your plugin folder *"/plugins/my_plugin/"*
1. Create a file *"notification.inc.php"*
1. Edit file and place your content
 
```php
<?php

Plugin::dashboardAddNotification('info|success|error', "Your message");
```

Et voilà ! 



  
  
  
  






