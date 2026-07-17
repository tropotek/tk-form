# Tk Form

__Project:__ [ttek/tk-form](http://packagist.org/packages/ttek/tk-form)
__Web:__ <http://www.tropotek.com/>  
__Authors:__ Michael Mifsud <http://www.tropotek.com/>  
  
A Form lib for the Tk Framework

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
    "ttek/tk-form": "~8.1.0"
  }
}
```


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




