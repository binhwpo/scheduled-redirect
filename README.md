# Scheduled Redirect

This WordPress plugin provides additional redirect action for [Scheduled Content Actions](https://github.com/dasllama/Scheduled-Content-Actions) plugin.

Making a brand new plugin would take time and effort. So I created this one based in extension to the Scheduled Content Actions so that the Redirect is created using the form with ajax saving. It's possible to create as many redirect actions as wanted

# Features:

It has 2 modes: 

1. Custom URL, and
2. Subpage selection.

Both method will be saved as redirect_url.

# How it works:

1. The time and redirect_url will be saved into DB together with any other Scheduled Content Actions. 
2. Then when a page is opened:
2.a If there is a redirect ation in future, the page will content a javascript code to redirect after a calculated time interval.
2.b Otherwise, the PHP will redirect the page immediately.

# Screenshots:

1. Redirect action added to Scheduled Content Actions form:
![redirect action added](https://user-images.githubusercontent.com/7647566/35867029-5506be92-0b8b-11e8-80f2-cc0885640938.jpg)

2. Page URL selector:
![page url selector](https://user-images.githubusercontent.com/7647566/35867030-553ce3aa-0b8b-11e8-861a-9a6e6a33a438.jpg)

3. Redirect action saved:
![redirect action saved](https://user-images.githubusercontent.com/7647566/35867031-5572635e-0b8b-11e8-8ec4-a27d0136949b.jpg)
