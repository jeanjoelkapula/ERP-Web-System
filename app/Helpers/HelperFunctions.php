<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\HTTP\RequestInterface;

class HelperFunctions
{

	protected $db;
	protected $request;

	public function __construct(ConnectionInterface &$db, RequestInterface $request)
	{
		$this->db = &$db;
		$this->request = $request;
	}


	public function clean_str($text, $escape = false)
	{

		$normal_characters = "a-zA-Z0-9 &@#()_+-={}|:;?,.\/\"\'\\\[\]";
		$normal_text = preg_replace("/[^$normal_characters]/", '', $text);

		$normal_text = preg_replace("/[[:blank:]]+/", " ", $normal_text);
		$normal_text = trim($normal_text);

		if ($escape) $normal_text = $this->db->escape($normal_text);

		return $normal_text;
	}

	public function clean_int($number, $decimal = false)
	{

		$normal_characters = "0-9";
		if ($decimal) $normal_characters = "0-9.";
		$normal_number = preg_replace("/[^$normal_characters]/", '', $number);

		if ($normal_number == '') $normal_number = 0;

		return $normal_number;
	}

	public function clean_post($post_var, $allow_line_break = false)
	{

		$value = $this->request->getPost($post_var);

		if (!$allow_line_break) {
			$allowed_characters = "/[^a-zA-Z0-9 &@#()_+-={}|:;?,.\/\"\'\\\[\]]/";
		} else {
			$allowed_characters = "/[^a-zA-Z0-9 &@#()_+-={}|:;?,.\\n\/\"\'\\\[\]]/";
		}

		$normal_text = preg_replace($allowed_characters, '', $value);
		$normal_text = preg_replace("/[[:blank:]]+/", " ", $normal_text);
		$normal_text = $this->db->escape(trim($normal_text));

		return $normal_text;
	}

	public function clean_post_int($post_var, $end = ',')
	{

		$value = $this->request->getPost($post_var);

		$normal_characters = "0-9";
		$normal_number = preg_replace("/[^$normal_characters]/", '', $value);

		if ($normal_number == '') $normal_number = 0;

		return $post_var . '=' . $normal_number . $end;
	}

	public function clean_post_str($post_var, $end = ',', $allow_line_break = false)
	{

		$value = $this->request->getPost($post_var);

		if (!$allow_line_break) {
			$allowed_characters = "/[^a-zA-Z0-9 &@#()_+-={}|:;?,.\/\"\'\\\[\]]/";
		} else {
			$allowed_characters = "/[^a-zA-Z0-9 &@#()_+-={}|:;?,.\\n\/\"\'\\\[\]]/";
		}

		$normal_text = preg_replace($allowed_characters, '', $value);
		$normal_text = preg_replace("/[[:blank:]]+/", " ", $normal_text);
		$normal_text = $this->db->escape(trim($normal_text));

		return $post_var . '=' . $normal_text . $end;
	}

	function array_to_string($list, $force_integer = false)
	{

		if (is_array($list)) {

			foreach ($list as &$value) {
				if (is_string($value) && !$force_integer) $value = $this->db->escape($value);
			}
			//array_walk($list, function(&$x) {$x = "'$x'";});
			return implode(',', $list);
		}

		if (is_string($list) && $force_integer) $list = $this->db->escape($list);

		return $list;
	}
	
	function print_pre ($expression, $return = false, $wrap = false){
		$css = 'border:1px dashed #06f;background:#ddd;padding:1em;text-align:left;';
		if ($wrap) {
			$str = '<p style="' . $css . '"><tt>' . str_replace(
				array('  ', "\n"), array('&nbsp; ', '<br />'),
				htmlspecialchars(print_r($expression, true))
			) . '</tt></p>';
		} else {
			$str = '<pre style="' . $css . '">'
			. htmlspecialchars(print_r($expression, true)) . '</pre>';
		}
		if ($return) {
		  if (is_string($return) && $fh = fopen($return, 'a')) {
				fwrite($fh, $str);
				fclose($fh);
		  }
		  return $str;
		} else
		  echo $str;
	}	
}
