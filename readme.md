# Tk Form

__Project:__ [ttek/tk-form](http://packagist.org/packages/ttek/tk-form)
__Web:__ <http://www.tropotek.com/>  
__Authors:__ Michael Mifsud <http://www.tropotek.com/>  
  
This is the lib for the Tk Framework HTML Forms

## Contents

- [Installation](#installation)
- [Introduction](#introduction)


## Installation

Available on Packagist ([ttek/tk-form](http://packagist.org/packages/ttek/tk-form))
and as such installable via [Composer](http://getcomposer.org/).

```bash
composer require ttek/tk-form
```

Or add the following to your composer.json file:

```json
{
  "require": {
    "ttek/tk-form": "~8.0.0"
  }
}
```


### TODO

- Implement fieldsets and tabgroups using javascript and tags data or role attributes
- Add more functionality to file uploads using jquery
- Implement a WYSIWYG editor in javascript, idealy using classes sowe can make them selectable
- Implement select2 for multi select fields


### Form State Chart

```
   Create Form
       |
       |
 Add Form Fields
       |
       |
 Load Field Values
       |
       |
 if (isSubmitted) ----[YES]---------------
       |                                 |
       |                                 |
      [No]                   Load Request Into Field values
       |                                 |
       |                                 |
       |                         Validate Field Data
       |                                 |
       |                                 |
       | <----------[YES]-------  if (hasErrors)
       |                                 |
       |                                 |
       |                                [No]
       |                                 |
       |                                 |
  Render Form                   Save Data To Storage    
                                         | 
                                         |
                               Redirect To Success Page
  
```




