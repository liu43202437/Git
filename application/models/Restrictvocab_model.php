<?php

// restrict_vocab table model
class Restrictvocab_model extends Base_Model {

	protected $tempfile = 'tmp/restrict_vocabularies.php';
	 
	// constructor
	public function __construct() 
	{
		parent::__construct();
		
		global $TABLE;
		$this->tbl = $TABLE['restrict_vocab'];
	}
	
	// insert
	public function insert($vocab) 
	{
		$this->deleteTempFile();
		$data = array(
			'vocab' => $vocab,
			'create_date' => now()
		);
		return $this->_insert($data);
	}
	// update
	public function update($id, $data, $table = null)
	{
		$this->deleteTempFile();
		return parent::update($id, $data, $table);
	}
	// delete
	public function delete($id, $table = null)
	{
		$this->deleteTempFile();
		return parent::delete($id, $table);
	}
	
	// delete temp file
	public function deleteTempFile()
	{
		if (file_exists($this->tempfile)) {
			unlink($this->tempfile);
		}
	}
	
	
	// get all words
	public function getAllVocabs()
	{
		if (file_exists($this->tempfile)) {
			
			include_once($this->tempfile);
			
		} else {
			$result = $this->getAll();
			$vocabularies = array();
			foreach ($result as $item) {
				$vocabularies[] = $item['vocab'];
			}
			
			$var_str = var_export($vocabularies, true);
			$var = "<?php defined('BASEPATH') OR exit('No direct script access allowed');
				\n\$vocabularies = $var_str;\n\n?>";
			file_put_contents($this->tempfile, $var);
		}
		
		return $vocabularies;
	}
	
	// get filtered string
	public function getFilteredContent($content)
	{
		if (empty($content)) {
			return $content;
		}

		$vocabs = $this->getAllVocabs();
		if (!empty($vocabs)) {
			foreach ($vocabs as $v) {
				//$pos = mb_strpos($content, $v);
				//if ($pos !== false) {
					$r = str_repeat("*", mb_strlen($v));
					$content = str_replace($v, $r, $content);
				//}
			}
		}
		return $content;
	}
}
?>
