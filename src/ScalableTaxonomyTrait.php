<?php namespace Bsapaka\ScalableTaxonomy;

use Bsapaka\EloquentAttribute\Attribute;
use Bsapaka\EloquentAttribute\AttributeList;
use Bsapaka\EloquentAttribute\AttributesTrait;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Support\Facades\Validator;

trait ScalableTaxonomyTrait {

	use AttributesTrait;
	use SingleTableInheritanceTrait;

	/**
	 * @var AttributeList
	 */
	protected $fullAttributeList;


	/**
	 * @return AttributeList
	 */
	public function getAttributeList() {
		return $this->fullAttributeList;
	}

	/**
	 * Find the leaf class and populate the AttributeList with real values for all attributes in lineage
	 *
	 * @param array $attributes
	 */
	public function hydrateAttributeList(array $attributes) {
		$this->fullAttributeList = AttributeList::make(
			$this->lineageAttributes(get_called_class())
		)->hydrate($attributes);
	}

	/**
	 * Return the defined attributes of the class and its ancestors
	 *
	 * @param $class
	 * @return array
	 */
	public static function lineageAttributes($class) {
		//get all ancestors
		$classes = class_parents($class);

		//setup AttributeList that includes attributes of ancestor classes
		$attributes = array_merge(array(),$class::defineAttributes());
		foreach($classes as $class) {
			if(method_exists($class, 'defineAttributes')) {
				$attributes = array_merge($attributes, $class::defineAttributes());
			}
		}

		return $attributes;
	}

	/**
	 * @return AttributeList
	 */
	public function attributeTemplate() {
		return new AttributeList(static::lineageAttributes(get_class($this)));
	}

	/**
	 * Get all leaves in the taxonomy
	 *
	 * @param bool|TRUE $fullyQualified
	 * @return array
	 */
	public static function leafClasses($fullyQualified = true) {
		$leafClasses = static::getSingleTableTypeMap();

		if($fullyQualified) {
			return $leafClasses;
		} else {
			return array_map('strtolower', array_keys($leafClasses));
		}
	}


	/**
	 * Return an array of all classes in the taxonomy
	 *
	 * @param bool|TRUE $fullyQualified
	 * @return array
	 */
	public static function allClasses($fullyQualified = true) {
		$taxons = array();

		//get all leaves
		$leaves = static::getSingleTableTypeMap();

		//put in array the ancestor classes of each leaf
		foreach($leaves as $className => $classPath) {
			$taxons[$className] = $classPath;
			foreach(class_parents($classPath) as $parentPath) {
				$r = new \ReflectionClass($parentPath);
				$parentName = strtolower($r->getShortName());
				if(!in_array($parentPath, $taxons) && !$r->isAbstract()) {
					$taxons[$parentName] = $parentPath;
				}
			}
		}

		if($fullyQualified) {
			return $taxons;
		} else {
			return array_map('strtolower', array_keys($taxons));
		}
	}


	/**
	 * Get subclasses of the class
	 * @return array
	 */
	public static function subClasses($fullyQualified = true) {
		$subClasses = array();

		$isfLeaf = property_exists(get_called_class(),'singleTableType');

		//if not a leaf, then return the subclasses
		if(!$isfLeaf) {
			foreach(static::$singleTableSubclasses as $classPath) {
				$r = new \ReflectionClass($classPath);
				$className = strtolower($r->getShortName());
				$subClasses[$className] = $classPath;
			}
		}

		if($fullyQualified) {
			return $subClasses;
		} else {
			return array_map('strtolower', array_keys($subClasses));
		}
	}

	/**
	 * Return an instance of a class in the taxonomy given its class name
	 *
	 * @param string $class
	 * @return mixed
	 */
	public static function instanceOfType($class) {
		$classes = static::allClasses();
		$class = $classes[$class];
		return new $class;
	}


	/**
	 * Get the classes of a class in the taxonomy given its class name
	 *
	 * @param $class
	 * @param bool|TRUE $fullyQualified
	 * @return mixed
	 */
	public static function subClassesOfType($class, $fullyQualified = true) {
		$classes = static::allClasses();
		$class = $classes[$class];
		return $class::subClasses($fullyQualified);
	}

	/**
	 * @return mixed
	 */
	public function validationRules() {
		return $this->attributeTemplate()->validationRules();

	}

	/**
	 * @param string $error
	 * @return string
	 */
	public static function formatError($error) {
		$formattedError = '';

		$attributes = static::lineageAttributes(get_called_class());

		/** @var Attribute $attribute */
		foreach ($attributes as $attribute) {
			if(str_contains($error, $attribute->getName())) {
				$formattedError = str_replace($attribute->getName(), $attribute->getAlias(), $error);
				break;
			}
		}

		return $formattedError;
	}

}