<?php namespace Bsapaka\ScalableTaxonomy;

use Illuminate\Support\Str;

class Validator extends \Illuminate\Validation\Validator {


	protected function doReplacements($message, $attribute, $rule, $parameters)
	{
		$value = $attribute;

		$message = str_replace(
			[':ATTRIBUTE', ':Attribute', ':attribute'],
			[Str::upper($value), Str::ucfirst($value), $value],
			$message
		);

		if (isset($this->replacers[Str::snake($rule)])) {
			$message = $this->callReplacer($message, $attribute, Str::snake($rule), $parameters);
		} elseif (method_exists($this, $replacer = "replace{$rule}")) {
			$message = $this->$replacer($message, $attribute, $rule, $parameters);
		}

		return $message;
	}

}