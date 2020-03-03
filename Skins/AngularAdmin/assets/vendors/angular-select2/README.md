https://github.com/willcrisis/angular-select2-old/blob/master/README.md

# Angular Select2
Select2 directive for AngularJS.   
This module depends on [Select2](https://select2.github.io/). Make sure to install it on your module.

## Installing
To install this module, run the following:

```
bower install willcrisis/angular-select2 --save
```

Or download `select2.js` file and add it to your project folder.   

After installing, add a reference to this module to your app:

```
angular.module('myModule', ['willcrisis.angular-auth']);
```

And make sure to add it to your `index.html` file:
```
<script src="bower_components/angular-select2/select2.js"></script>
```

## Using

Using this directive is very simple. Just add it to your `<select>` element, like this:

```
<select select2 ng-model="myModel" ng-options="myObject.name for myObject in myObjectList"></select>
```

You can see a working demo [here](https://plnkr.co/edit/3diw9uQ0IeEv9vCxI22v)

Or you can clone this repo, run `bower install` in clone folder and start a simple web server (like lserver).   
Then navigate to `http://localhost:<server-port>/demo.html`

## Contributing

If you want to contribute with this project, feel free to open issues and fork the project.