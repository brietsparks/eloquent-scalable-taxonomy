<?php namespace Bsapaka\ScalableTaxonomy;

use Bsapaka\EloquentAttribute\AttributesTrait;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

trait ScalableTaxonomyTrait {

	use AttributesTrait;
	use SingleTableInheritanceTrait;

	/**
	 * @param array $attributes
	 */
	public function hydrateAttributeList(array $attributes) {
		$typeField = static::$singleTableTypeField;

		$classType = isset($attributes[$typeField]) ? $attributes[$typeField] : null;

		if ($classType !== null) {
			$childTypes = static::getSingleTableTypeMap();
			if (array_key_exists($classType, $childTypes)) {
				$class = $childTypes[$classType];
			}
		}

		$classes = class_parents($class);

		$attributes = array();
		foreach($classes as $class) {
			if(method_exists($class, 'defineAttributes')) {
				$attributes = array_merge($attributes, $class::defineAttributes());
			}
		}

		static::getAttributeList()->add($attributes);

	}
}