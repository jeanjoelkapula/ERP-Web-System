<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014-2019 British Columbia Institute of Technology
 * Copyright (c) 2019-2020 CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author     CodeIgniter Dev Team
 * @copyright  2019-2020 CodeIgniter Foundation
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://codeigniter.com
 * @since      Version 4.0.0
 * @filesource
 */

namespace CodeIgniter\Validation;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
use CodeIgniter\View\RendererInterface;

/**
 * Validator
 */
class Validation implements ValidationInterface
{

	/**
	 * Files to load with validation functions.
	 *
	 * @var array
	 */
	protected $ruleSetFiles;

	/**
	 * The loaded instances of our validation files.
	 *
	 * @var array
	 */
	protected $ruleSetInstances = [];

	/**
	 * Stores the actual rules that should
	 * be ran against $data.
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * The data that should be validated,
	 * where 'key' is the alias, with value.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Any generated errors during validation.
	 * 'key' is the alias, 'value' is the message.
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Stores custom error message to use
	 * during validation. Where 'key' is the alias.
	 *
	 * @var array
	 */
	protected $customErrors = [];

	/**
	 * Our configuration.
	 *
	 * @var \Config\Validation
	 */
	protected $config;

	/**
	 * The view renderer used to render validation messages.
	 *
	 * @var RendererInterface
	 */
	protected $view;

	//--------------------------------------------------------------------

	/**
	 * Validation constructor.
	 *
	 * @param \Config\Validation $config
	 * @param RendererInterface  $view
	 */
	public function __construct($config, RendererInterface $view)
	{
		$this->ruleSetFiles = $config->ruleSets;

		$this->config = $config;

		$this->view = $view;
	}

	//--------------------------------------------------------------------

	/**
	 * Runs the validation process, returning true/false determining whether
	 * validation was successful or not.
	 *
	 * @param array  $data     The array of data to validate.
	 * @param string $group    The pre-defined group of rules to apply.
	 * @param string $db_group The database group to use.
	 *
	 * @return boolean
	 */
	public function run(array $data = null, string $group = null, string $db_group = null): bool
	{
		$data = $data ?? $this->data;

		// i.e. is_unique
		$data['DBGroup'] = $db_group;

		$this->loadRuleSets();

		$this->loadRuleGroup($group);

		// If no rules exist, we return false to ensure
		// the developer didn't forget to set the rules.
		if (empty($this->rules))
		{
			return false;
		}

		// Replace any placeholders (i.e. {id}) in the rules with
		// the value found in $data, if exists.
		$this->rules = $this->fillPlaceholders($this->rules, $data);

		// Need this for searching arrays in validation.
		helper('array');

		// Run through each rule. If we have any field set for
		// this rule, then we need to run them through!
		foreach ($this->rules as $rField => $rSetup)
		{
			// Blast $rSetup apart, unless it's already an array.
			$rules = $rSetup['rules'] ?? $rSetup;

			if (is_string($rules))
			{
				$rules = $this->splitRules($rules);
			}

			$value          = dot_array_search($rField, $data);
			$fieldNameToken = explode('.', $rField);

			if (is_array($value) && end($fieldNameToken) === '*')
			{
				foreach ($value as $val)
				{
					$this->processRules($rField, $rSetup['label'] ?? $rField, $val ?? null, $rules, $data);
				}
			}
			else
			{
				$this->processRules($rField, $rSetup['label'] ?? $rField, $value ?? null, $rules, $data);
			}
		}

		return ! empty($this->getErrors()) ? false : true;
	}

	//--------------------------------------------------------------------

	/**
	 * Check; runs the validation process, returning true or false
	 * determining whether validation was successful or not.
	 *
	 * @param mixed    $value  Value to validation.
	 * @param string   $rule   Rule.
	 * @param string[] $errors Errors.
	 *
	 * @return boolean True if valid, else false.
	 */
	public function check($value, string $rule, array $errors = []): bool
	{
		$this->reset();
		$this->setRule('check', null, $rule, $errors);

		return $this->run([
			'check' => $value,
		]);
	}

	//--------------------------------------------------------------------

	/**
	 * Runs all of $rules against $field, until one fails, or
	 * all of them have been processed. If one fails, it adds
	 * the error to $this->errors and moves on to the next,
	 * so that we can collect all of the first errors.
	 *
	 * @param string       $field
	 * @param string|null  $label
	 * @param string|array $value Value to be validated, can be a string or an array
	 * @param array|null   $rules
	 * @param array        $data  // All of the fields to check.
	 *
	 * @return boolean
	 */
	protected function processRules(string $field, string $label = null, $value, $rules = null, array $data): bool
	{
		// If the if_exist rule is defined...
		if (in_array('if_exist', $rules))
		{
			// and the current field does not exists in the input data
			// we can return true. Ignoring all other rules to this field.
			if (! array_key_exists($field, $data))
			{
				return true;
			}
			// Otherwise remove the if_exist rule and continue the process
			$rules = array_diff($rules, ['if_exist']);
		}

		if (in_array('permit_empty', $rules))
		{
			if (! in_array('required', $rules) && (is_array($value) ? empty($value) : (trim($value) === '')))
			{
				$passed = true;

				foreach ($rules as $rule)
				{
					if (preg_match('/(.*?)\[(.*)\]/', $rule, $match))
					{
						$rule  = $match[1];
						$param = $match[2];

						if (! in_array($rule, ['required_with', 'required_without']))
						{
							continue;
						}

						// Check in our rulesets
						foreach ($this->ruleSetInstances as $set)
						{
							if (! method_exists($set, $rule))
							{
								continue;
							}

							$passed = $passed && $set->$rule($value, $param, $data);
							break;
						}
					}
				}

				if ($passed === true)
				{
					return true;
				}
			}

			$rules = array_diff($rules, ['permit_empty']);
		}

		foreach ($rules as $rule)
		{
			$callable = is_callable($rule);
			$passed   = false;

			// Rules can contain parameters: max_length[5]
			$param = false;
			if (! $callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match))
			{
				$rule  = $match[1];
				$param = $match[2];
			}

			// Placeholder for custom errors from the rules.
			$error = null;

			// If it's a callable, call and and get out of here.
			if ($callable)
			{
				$passed = $param === false ? $rule($value) : $rule($value, $param, $data);
			}
			else
			{
				$found = false;

				// Check in our rulesets
				foreach ($this->ruleSetInstances as $set)
				{
					if (! method_exists($