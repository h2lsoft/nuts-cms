# Commands

Commands are auto parsed in your website content

## Commands (separator 4 spaces or tab)

```code
{@NUTS    TYPE='PAGE'    CONTENT='URL'    ID='?'}
```

return page url for ID

```code
{@NUTS    TYPE='PAGE'    CONTENT='MENU_NAME'    ID='?'}
```

return page content for ID

```code
{@NUTS    TYPE='MENU'    CONTENT='ALL CHILDRENS'    ID='?'    OUTPUT='LI>UL'    CSS='?'    ATTRIBUTES=''    INCLUDE_PARENT='0|1'}
```

return complete menu from page ID


```code
{@NUTS    TYPE='MENU'    CONTENT='ALL CHILDRENS'    OUTPUT='LI>UL'    CSS='?'    ATTRIBUTES=''    INCLUDE_PARENT='0|1'}
```

return complete menu for a zone


```code
{@NUTS    TYPE='PLUGIN'    NAME='?'[    PARAMETERS='param1, param2'}
```

return plugin frontend content (optionnal parameters)


```code
{@NUTS    TYPE='NAVBAR'    SEPARATOR='|'}
```
 
return complete breadcrumbs
 
 
```code
{@NUTS    TYPE='LIST-IMAGES'    NAME='MENU_NAME'    ID='?'    MENU_VISIBLE='1'    POSITION='ASC|DESC|RAND']    LIMIT='10']}
```

return a complete list image

```code
{@NUTS    TYPE='REGION'    NAME='?'    PARAMETERS='param 1, ...'}
```

return complete region, use *"PARAMETER_N"* to parse in sql region


```code
{@NUTS    TYPE='GALLERY'    NAME='?'}
```

return complete image gallery

```code
{@NUTS    TYPE='BLOCK'    GROUP='?'}
```

return all blocs defined in page manager for current page

```code
{@NUTS    TYPE='BLOCK'    NAME='?'}
```

return block content


```code
{@NUTS    TYPE='MEDIA'    OBJECT='AUDIO'    NAME='?'}
```

return media audio
 
 
```code
{@NUTS    TYPE='MEDIA'    OBJECT='VIDEO'    NAME='?'}
```

return media video



## Special commands

```code
{#include('/path/_file.html');}
```

include a file in template


```code
{#if(php_condition)}
    some text
{#elseif(php_condition)}
    some text
{#endif}
```

Php if operator in template

```code
{$NUTS_CONTENT}
```

replace by template parsing, useful for plugin


```code
{$page->vars[array_key]}
```

replace by php variable in template

















 






