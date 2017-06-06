# Commands

Commands are auto parsed in your website content

## Commands (separator 4 spaces or tab)

```{@NUTS    TYPE='PAGE'    CONTENT='URL'    ID='?'}```

return page url for ID

```{@NUTS    TYPE='PAGE'    CONTENT='MENU_NAME'    ID='?'}```

return page content for ID

```{@NUTS    TYPE='MENU'    CONTENT='ALL CHILDRENS'    ID='?'    OUTPUT='LI>UL'    CSS='?'    ATTRIBUTES=''    INCLUDE_PARENT='0|1'}```

return complete menu from page ID


```{@NUTS    TYPE='MENU'    CONTENT='ALL CHILDRENS'    OUTPUT='LI>UL'    CSS='?'    ATTRIBUTES=''    INCLUDE_PARENT='0|1'} ```

return complete menu for a zone


```{@NUTS    TYPE='PLUGIN'    NAME='?'[    PARAMETERS='param1, param2'}```

return plugin frontend content (optionnal parameters)


```{@NUTS    TYPE='NAVBAR'    SEPARATOR='|'}```
 
return complete breadcrumbs
 
 
```{@NUTS    TYPE='LIST-IMAGES'    NAME='MENU_NAME'    ID='?'    MENU_VISIBLE='1'    POSITION='ASC|DESC|RAND']    LIMIT='10']}```

return a complete list image

```{@NUTS    TYPE='REGION'    NAME='?'    PARAMETERS='param 1, ...'}```

return complete region, use *"PARAMETER_N"* to parse in sql region


```{@NUTS    TYPE='GALLERY'    NAME='?'}```

return complete image gallery

```{@NUTS    TYPE='BLOCK'    GROUP='?'}```

return all blocs defined in page manager for current page

```{@NUTS    TYPE='BLOCK'    NAME='?'}```

return block content


```{@NUTS    TYPE='MEDIA'    OBJECT='AUDIO'    NAME='?'}```

return media audio
 
 
```{@NUTS    TYPE='MEDIA'    OBJECT='VIDEO'    NAME='?'}```

return media video



## Special commands

```{#include('/path/_file.html');}```

include a file in template


```
{#if(php_condition)}
    some text
{#elseif(php_condition)}
    some text
{#endif}
```

Php if operator in template

```{$NUTS_CONTENT}```

replace by template parsing, useful for plugin


```{$page->vars[array_key]}```

replace by php variable in template

















 






