# Eloquent Scalable Taxonomy

Create a [single table inheritance](https://github.com/Nanigans/single-table-inheritance) model structure than can be easily scaled with any size taxonomy tree. Any given tree only needs one view because displaying the model attributes can be done via abstraction. Each attribute is an object of the [EloquentAttribute\Attribute](https://github.com/bsapaka/eloquent-attribute-class) class, which allows you to define and access each attribute as a class.


## Install

Via Composer
``` 
composer require bsapaka/eloquent-scalable-taxonomy
```

## Usage ##

Refer to [this example app](https://github.com/bsapaka/example-eloquent-scalable-taxonomy).
