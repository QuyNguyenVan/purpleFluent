<?php
/**
 * Purple Fluent data manipulation tools
 * @version 0.1
 * @author eka808 (http://www.yoannmagli.ch)
**/

/** 
  * The base call class 
 **/
class Purple
{
	public static function InitializeEnumerable()
	{
		$args = func_get_args();
		$reflector = new ReflectionClass('FluentEnumerable');
		return $reflector->newInstanceArgs($args);;
	}
}


/** 
 * Common code between the single and enumerable values
**/
abstract class AbstractFluent
{
	/** Underlying data store property */
	protected $val;
	
	/** Default Constructor **/
	function __construct($_val=null) 
	{ 
		$this->val = $_val; 
	}

	/** Show a debug trace of this to the standard output */
	function toDebug() { 
		echo "<pre>";
			print_r($this); 
		echo "</pre>";
		return $this; 
	}

	/** Pretty rendering for echo */
	/*function __toString() 
	{
		return $this->val; 
	}*/
	
	/** Get the underlying object */
	/*function __invoke() 
	{ 
		return $this->val; 
	}*/
}

/**
 * Represent a unique value as fluent manipulable
**/
class FluentValue extends AbstractFluent
{
	//// GETTERS ////

	/** Magic method to get a property of the object */
	function __get($propName = null) 
	{ 
		return $this->val->$propName; 
	}

	//// MODIFIERS ////

	/** Up the string value */
	public function toUpper() 
	{ 
		$this->val = strtoupper($this->val);	
		return $this;
	}

	/** str_replace fluent implementation **/
	public function Replace($search, $replace) 
	{
		$this->val =  str_replace($search, $replace, $this->val); 
		return $this; 
	}

	/** preg_replace fluent implementation **/
	public function PregReplace($pattern, $replace)
	{
		$this->val = preg_replace($pattern, $replace, $this->val); 
		return $this;
	}			

	//// EXPORTERS ////

	/** Export underlying data to Json **/
	public function toJson() 
	{
		return json_encode($this->val);
	}	
}

/**
 * Represent an enumerable as fluent manipulable
**/
class FluentEnumerable extends AbstractFluent
{
	//// SETTERS ////
	
	/** From a regex delimiter pattern and a raw data string, set the enumerable **/
	public function FromPattern($rawData, $delimiterPattern) 
	{
		$this->val = preg_split($delimiterPattern, $rawData); 
		return $this;
	}	
	
	/** From a text file, set the enumerable by splitting by neyline code */
	public function FromTextFile($uri)
	{
		$this->val = file($uri); 
		return $this;
	}
	
	//// MODIFIERS ////

	/** Remove an item from the enumerable */
	public function RemoveItem($key) 
	{ 
		unset($this->val[$key]); 
		return $this; 
	}
	
	/** Execute a closure on an unique property **/
	public function ForOneItem($key, $actionClosure)
	{
		$actionClosure($this->val[$key]); 
		return $this;
	}

	/** Execute a closure on the elements of the enumerable **/
	public function Each($functionToMap) 
	{
		$return = [];
		foreach ($this->val as $key => $value)
			$return[$key] = $functionToMap($value);
		$this->val = $return;
		return $this;
	}

	/** Convert an indexed enumerable to a associated enumerable by using names passed in parameter **/
	public function SetPropertyNames($keys)
	{
		$returnData = [];
		foreach ($this->val as $key => $value) 
		if ($key <= count($keys))
			$returnData[$keys[(int)$key]] = $value;
		$this->val = $returnData;
		return $this;
	}

	/** Transform an enumerable to a value object **/	
	public function toObject($type = 'FluentValue') 
	{
		$this->val = new $type((object)$this->val);
		return $this->val;
	}
	
	//// GETTERS ////

	/** Select elements who correspond to the closure */
	public function Where($f)
	{
		$return = [];
		foreach ($this->val as $key => $value)
		if ($f($value))	
			$return[] = $value;
		$this->val = $return;
		return $this;
	}

	//// PROJECTIONS ////

	/** Select elements and apply a closure to them **/
	public function Select($functionToMap) 
	{
		$return = [];
		foreach ($this->val as $key => $value)
			$return[$key] = $functionToMap($value);
		return $this;
	}

	/** Project the underlying value to JSON */
	public function toJson() 
	{
		$returnData = '[';
		$i=0;
		foreach ($this->val as $key => $value) 
		{
			$i++;
			$returnData .= $value->toJson();
			if ($i < count($this->val))
			 	$returnData .= ',';
		}
		$returnData .= ']';
		return $returnData;
	}
}
?>