# Ajax

Nuts allows you to isolate part of rendered template easily.

Just place this markup in your template

```html
<!-- ajax::my_bloc -->
part of template to extract
<!-- /ajax::my_bloc -->
```


To get the content just call your url with these parameters :

```
MY_PAGE_URL?ajaxer=1&ajaxer_bloc=my_bloc
```




