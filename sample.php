<?php
	include_once('purpleFLuent.php');

	header('Content-type: application/json');
	echo (
			Purple::InitializeEnumerable(/*['1','2','3']*/)
			->FromTextFile("genevaList")
			->Each(function($o) { return trim($o); })
			->Where(function($o) { return $o != ''; })
			->Where(function($o) { return preg_match("#var (.+) = \[#", $o) == false; })
			->Where(function($o) { return $o != '];'; })
			->Each(function($o) { return preg_replace("#, [0-9], '?.+', '?.+'#", '', $o); })
			->Each(function($o) { return str_replace('],', '', $o); })
			->Each(function($o) { return str_replace('[', '', $o); })
			->Each(function($o) { return str_replace("'", '', $o); })
			->Each(function($o) { return str_replace(']', '', $o); })
			->Each(function($o) { return preg_split("#,#", $o); })
			->Each(function($o) { 
				return 
					Purple::InitializeEnumerable($o)
					->SetPropertyNames(['Cat','Nom','Lat','Long'])
					->Each(function($y) {return utf8_encode(utf8_decode($y)); })
					->toObject();
			})
			//->Select(function($o) { print_r($o); })		
			->toJson()
			//->toDebug()
		);
?>