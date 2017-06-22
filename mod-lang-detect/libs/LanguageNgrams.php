<?php
// Class: \SmartModExtLib\LangDetect\LanguageNgrams
// Ngrams Language Detection :: for Smart.Framework
// Module Library
// v.3.5.1 r.2017.05.12 / smart.framework.v.3.5

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\LangDetect;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//======================================================
// Language Detection - Ngrams
// DEPENDS:
//	* Smart::
//	* SmartUnicode::
//	* SmartFileSystem::
//	* SmartFileSysUtils::
//======================================================


//==================================================================================================
//================================================================================================== START CLASS
//==================================================================================================


// This class is based on the following project: PHP LanguageDetection
// https://github.com/patrickschur/language-detection
// (c) 2016-2017 Patrick Schur
// License: MIT


/**
 * Class LanguageNgrams
 *
 */
final class LanguageNgrams {


	/**
	 * @var int
	 */
	private $minLength = 1; // default is 1


	/**
	 * @var int
	 */
	private $maxLength = 3; // default is 3


	/**
	 * @var int
	 */
	private $maxNgrams = 310; // default is 310


	/**
	 * @var array
	 */
	private $tokens = [];


	/**
	 * Class Constructor
	 *
	 * @param STRING 		$ngrams_path 		The path to the resources or NULL if only need to train
	 * @param ARRAY 		$lang 				The Array of languages to detect for OR an empty array to try detect all available languages
	 */
	public function __construct($ngrams_path='modules/mod-lang-detect/libs/data-1-3-310', $lang=[]) {

		if($ngrams_path === null) {
			return;
		} //end if

		$ngrams_path = \SmartFileSysUtils::add_dir_last_slash($ngrams_path);

		$lang = (array) $lang;
		if(\Smart::array_size($lang) <= 0) {
			$arr_fs = (array) (new \SmartGetFileSystem(true))->get_storage((string)$ngrams_path, false, false, '');
			if(\Smart::array_size($arr_fs['list-dirs']) > 0) {
				$lang = (array) $arr_fs['list-dirs'];
			} //end if
			$arr_fs = array();
		} //end if
		//print_r($lang); die();

		if(\Smart::array_size($lang) > 0) {
			for($i=0; $i<\Smart::array_size($lang); $i++) {
				$json_file = (string) \SmartFileSysUtils::add_dir_last_slash((string)$ngrams_path.\Smart::safe_filename((string)$lang[$i])).\Smart::safe_filename((string)$lang[$i]).'.json';
				if(is_file($json_file)) {
					$json = (string) \SmartFileSystem::staticread((string)$json_file);
					if((string)$json != '') {
						$json = \Smart::json_decode((string)$json);
						if(\Smart::array_size($json) > 0) {
							if(\Smart::array_size($json[(string)$lang[$i]]) > 0) {
								$this->tokens += (array) $json;
							} //end if
						} //end if
					} //end if
				} //end if
			} //end for
		} //end if
		//print_r($this->tokens); die();

	} //END FUNCTION


	/**
	 * Detects and Get the Language Confidence information for the best detected Language (from the available list) for a given text
	 *
	 * @param STRING 		$str				The text to be checked
	 * @return ARRAY 							The detection result: [ service-available, lang-id, confidence-score, error-message ]
	 *
	 */
	public function getLanguageConfidence($str) {

		$arr = (array) $this->detect((string)$str);

		$errmsg = '';
		if(\Smart::array_size($arr) <= 0) {
			$errmsg = 'Language Detection Failed to find Language Data ...';
		} //end if else

		$langid = '';
		$score = -1;
		foreach($arr as $key => $val) {
			$langid = (string) $key;
			$score = $val;
			break;
		} //end foreach

		return (array) [
			'service-available' => (bool)   true, // this is always TRUE here, it is not a remote but internal service
			'lang-id' 			=> (string) substr((string)strtolower((string)trim((string)$langid)), 0, 2),
			'confidence-score' 	=> (string) \Smart::format_number_dec((float)$score, 5, '.', ''),
			'error-message' 	=> (string) $errmsg
		];

	} //END FUNCTION


	/**
	 * Detects the language from a given text string
	 *
	 * @access 		private
	 * @internal
	 *
	 * @param STRING 		$str 			The text string to detect
	 * @return ARRAY 						The detection results
	 */
	public function detect($str) {

		$str = (string) \SmartUnicode::str_tolower((string)$str);

		$ngrams = (array) $this->getNgrams($str);

		$result = [];
		if(count($ngrams) > 0) {
			foreach ($this->tokens as $lang => $value) {
				$index = $sum = 0;
				$value = array_flip($value);
				foreach($ngrams as $v) {
					if(isset($value[$v])) {
						$x = $index++ - $value[$v];
						$y = $x >> (PHP_INT_SIZE * 8);
						$sum += ($x + $y) ^ $y;
						continue;
					} //end if
					$sum += $this->maxNgrams;
					++$index;
				} //end foreach
				$calc = ($sum / ($this->maxNgrams * $index));
				if($calc > 1) {
					$calc = 1;
				} elseif($calc < 0) {
					$calc = 0;
				} //end if else
				$result[$lang] = 1 - $calc;
			} //end foreach
			arsort($result, SORT_NUMERIC); // reverse sort array, numeric
		} //end if

		return (array) $result;

	} //END FUNCTION


	/**
	 * Train a language resource from a given text string
	 *
	 * @access 		private
	 * @internal
	 *
	 * @param STRING 		$str 			The text string to detect
	 * @return ARRAY 						The detection results
	 */
	public function train($lang, $str) {

		return (string) \Smart::json_encode(
			[
				(string) $lang => (array) $this->getNgrams((string)$str)
			], // array
			false, // no pretty print
			true, // unescaped unicode
			false // no need for HTML Safe
		);

	} //END FUNCTION


	/**
	 * Calculate Ngrams for a text string
	 *
	 * @access 		private
	 * @internal
	 *
	 * @param STRING 		$str 			The text string to calculate Ngrams for
	 * @return ARRAY 						The Ngrams calculations results
	 */
	public function getNgrams($str) {

		$str = (string) $str;

		$tokens = [];

		foreach($this->tokenize($str) as $k => $word) {
			$l = mb_strlen($word);
			for($i=$this->minLength; $i<=$this->maxLength; ++$i) {
				for($j=0; ($i+$j-1) < $l; ++$j, ++$tmp) {
					$tmp =& $tokens[$i][mb_substr($word, $j, $i)];
				} //end for
			} //end for
		} //end foreach

		if(!count($tokens)) {
			return [];
		} //end if

		foreach($tokens as $i => $token) {
			$sum = array_sum($token);
			foreach($token as $j => $value) {
				$tokens[$i][$j] = number_format($value / $sum, 12, '.', '');
			} //end foreach
		} //end foreach

		$tkns = (array) $tokens;
		$tokens = array(); // free mem
		foreach($tkns as $key => $val) {
			if(is_array($val)) {
				//$tokens = array_merge((array)$tokens, (array)$val);
				$tokens += (array) $val; // use array union to avoid re-index numeric keys if any
			} //end if
		} //end foreach
		$tkns = array(); // free mem

		unset($tokens['_']); // the tokenizer word limit char itself must be unset

		arsort($tokens, SORT_NUMERIC);
		//print_r($tokens); die();

		return (array) array_slice(
			array_keys($tokens),
			0,
			$this->maxNgrams
		);

	} //END FUNCTION


	/**
	 * Set the Ngrams Minimum Length
	 *
	 * @param INTEGER+ 		$minLength 		Min Ngrams Length: Default is 1
	 * @return VOID
	 */
	public function setMinLength($minLength) {

		$minLength = (int) $minLength;
		if($minLength <= 0 || $minLength >= $this->maxLength) {
			\Smart::log_warning(__METHOD__.': $minLength must be greater than zero and less than $this->maxLength.');
			return;
		} //end if

		$this->minLength = $minLength;

	} //END FUNCTION


	/**
	 * Set the Ngrams Maximum Length
	 *
	 * @param INTEGER+ 		$maxLength 		Max Ngrams Length: Default is 3
	 * @return VOID
	 */
	public function setMaxLength($maxLength) {

		$maxLength = (int) $maxLength;
		if($maxLength <= $this->minLength) {
			\Smart::log_warning(__METHOD__.': $maxLength must be greater than $this->minLength.');
			return;
		} //end if

		$this->maxLength = $maxLength;

	} //END FUNCTION


	/**
	 * Set the Maximum (significant) Ngrams
	 *
	 * @param INTEGER+ 		$maxNgrams 		Max Ngrams: Default is 310
	 * @return VOID
	 */
	public function setMaxNgrams($maxNgrams) {

		$maxNgrams = (int) $maxNgrams;
		if($maxNgrams <= 100) {
			\Smart::log_warning(__METHOD__.': $maxNgrams must be at least 100.');
			return;
		} //end if

		$this->maxNgrams = $maxNgrams;

	} //END FUNCTION


	/**
	 * Tokenize a text string
	 *
	 * @param STRING $str
	 * @return ARRAY
	 */
	private function tokenize($str) {

		$str = (string) $str;

		return (array) array_map(function ($word) {
				return "_{$word}_";
			},
			preg_split('/[^\pL]+(?<![\x27\x60\x{2019}])/u', (string)$str, -1, PREG_SPLIT_NO_EMPTY)
		);

	} //END FUNCTION


} //END CLASS


/***** Sample Usage:

// DETECT
$lndet = new \SmartModExtLib\LangDetect\LanguageNgrams();
$lndet->setMaxNgrams(20000);
$arr = $lndet->detect(SmartFileSystem::staticread('ngrams-res/en/en.txt'));
print_r($arr); die();

// TRAIN
$lndet = new \SmartModExtLib\LangDetect\LanguageNgrams(null);
$lndet->setMaxNgrams(20000);
$arr = $lndet->train('eng', \SmartFileSystem::staticread('ngrams-resources/eng/eng20k.txt'));
print_r($arr); die();

*****/


//==================================================================================================
//================================================================================================== START END
//==================================================================================================


// end of php code
?>