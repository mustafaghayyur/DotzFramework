# routerlib
A nifty library to help you build MVC apps quickly.


# Installation Instructions
1) Copy/Paste code from index.php to the index.php file on your application. 

2) Modify $routerDirectory value in this code to match where you have positioned the router directory in your application setup.

3) Open up router/jsonn.txt and modify the settings there as needed.

4) Be sure to copy over the .htaccess to your app's root folder!

# json.txt Settings

- appURL: your full http:// url for the app.
- controllersDirectory: the directory where all your controllers will reside. Note: all controllers must reside within this defined directory.
- controllerBased: this should remain 'true'. It allows you to automatically create urls 'ControllerName/Method_Name' without adding any definitions in the json.txt file.
- ErrorPage: the html file containing your 404 Error Page when a user reaches a url not defined.

- customRules: Follow the pattern already laid out in the sample json.txt to create more custom URLs. All custom URLs can have only one uri element (i.e. no 'sometext/othertext' URIs should be defined).
-- Start by defining the slug the user would enter in the URL to reach this page
-- Then in a json Object define the 'controller' name and 'method' name that will render the page for this custom URI
Custom URLs are handy when you need them for SEO purposes or quick user-access

- restResources: Follow the pattern already laid out in  the sample json.txt to create more custom URLs for your RESTful resources. All RESTful Resource URLs can have only one URI element (i.e. no 'sometext/othertext' URIs should be defined).
-- Start by defining the slug the user would enter in the URL to reach this RESTful resource. eg. www.yoursite.com/people -> in this example 'people' is the name/slug/uri that you are defining 
-- Then as a value pair for this restResource define the controller name which will carry all the methods to access all the HTTP methods for this resource.


**Please note: Sample controllers are provided to show how this library would function.**