Styling Admin
=============

Install npm: [get-npm](https://www.npmjs.com/get-npm)

Ensure npm and gulp are up to date.

```
$ npm i -g npm
$ npm install gulp
```

Install the node dependencies.

```
$ cd apiopenstudio
$ npm install
```
    
Edit and Compile.

```
$ gulp {all,watch,js,css,img}
```

The gulpfile.js includes compilation of sass and minification of js and css
files.

You can add your own css to ```/src/css/main.css```.