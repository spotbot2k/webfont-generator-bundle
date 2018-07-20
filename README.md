[![](https://img.shields.io/packagist/v/spotbot2k/webfont-generator-bundle.svg?style=flat)](https://packagist.org/packages/spotbot2k/webfont-generator-bundle)

# Contao 4 Webfont Generator Bundle

Helps you to manage your self-hosted webfonts as an alternative to the Google fonts API.

## Why

As you might know, the usage of Google Services is not fully cmplient with the new GPDR, so hosting your own fonts is safer. As a bonus you can quickly switch fonts and styles in your layout.

### Installation

##### Using Contao Manager

Just find it, install and update your database

##### Manual installation

Via ssh go to your contao directory (not webroot, aka "/web") and type

```
composer require spotbot2k/webfont-generator-bundle
```

#### Things to be aware of

File picker will notwork in contao 4.3. It is about time to upgrae anyway.

Please use only free or properly licensed fonts.

#### Where to get fonts

[fontsquirrel.com](https://www.fontsquirrel.com/)
[fontlibrary.org](https://fontlibrary.org/)

#### What does work

- Generate and load font CSS
- Manage font styles (bold, italic etc.)
- Assign fonts to CSS selectors automaticly
- Tested on Contao 4.3.11, 4.4.20, 4.5.10

#### TODO

- Translate (check german, add russian)
- Find more reliable way to generate path
- Make exported CSS relative to allow import on a different installation
