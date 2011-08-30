<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('attribute/types/default/controller');

class MultiFileSelectorAttributeTypeController extends DefaultAttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';

	public function form() {		
		$this->set('mps', Loader::helper('form/multi_file_selector', 'multi_file_selector'));
		$this->getValue();
	}
	
	public function getValue(){
		$sql = "SELECT fID FROM atMultiFileSelectorItem WHERE avID=".intval($this->getAttributeValueID()).' ORDER BY position ASC';
		$db = Loader::db();
		$this->fIDArray = $db->getCol($sql);
		$this->set('fIDArray', $this->fIDArray);
		return $this->fIDArray;	
	}
	
	public function validateForm($data){
		$e = Loader::helper('validation/error');
		
		return (count($data['fIDArray']) > 1);
	}
	
	public function saveForm($data){
		//Log::addEntry(print_r($data, TRUE));
		$this->saveValue($data['fIDArray']);
	}
	
	
	public function saveValue($fIDArray){		
		if(count($fIDArray) > 0){
			$db = Loader::db();
			$avID = intval($this->getAttributeValueID());
			//Delete existing pages 
			$db->query('DELETE FROM atMultiFileSelectorItem WHERE avID IN('.$avID.',0)');
			
			//Add the pages
			$pos = 0;
			foreach($fIDArray as $fID){
				$vals = array($avID,intval($fID), $pos);
				$db->query("INSERT INTO atMultiFileSelectorItem (avID,fID,position) values (?,?,?)",$vals);
				//Log::addEntry(print_r($vals, TRUE));
				$pos++;
			}				
		}
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->query("DELETE FROM atMultiFileSelectorItem WHERE avID=".intval($this->getAttributeValueID()));		
		parent::deleteValue();
	}
	
	
	
	function pre($thing, $save=FALSE){
		$str = '<pre style="white-space:pre; border:1px solid #ccc; padding:8px; margin:0 0 8px 0;">'.print_r($thing, TRUE).'</pre>';
		if(!$save){
			echo $str;	
		}
		return $str;
	}

}