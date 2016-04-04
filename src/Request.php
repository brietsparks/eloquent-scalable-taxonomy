<?php namespace Bsapaka\ScalableTaxonomy;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest {

	protected function getValidatorInstance()
	{
		$factory = $this->container->make(ValidatorFactory::class);

		if (method_exists($this, 'validator')) {
			return $this->container->call([$this, 'validator'], compact('factory'));
		}

		return $factory->make(
			$this->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
		);
	}

}