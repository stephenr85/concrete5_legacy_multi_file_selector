<?php defined('C5_EXECUTE') or die("Access Denied.");
	class MultiFileSelectorBlockController extends BlockController {
		
		protected $btDescription = "Select and order a specific pages.";
		protected $btName = "Multi-File Selector";
		protected $btTable = 'btMultiFileSelector';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "300";
		
		
		function loadExistingLabels(){
			$sql = "SELECT DISTINCT label FROM btMultiFileSelector ORDER BY label ASC";	
			$db = Loader::db();
			$this->existingLabels = $db->getCol($sql);
			$this->set("existingLabels", $this->existingLabels);
		}
		
		function loadCollectionIDArray(){
			$sql = "SELECT fID FROM btMultiFileSelectorItem WHERE bID=".intval($this->bID).' ORDER BY position ASC';
			$db = Loader::db();
			$this->fIDArray=$db->getCol($sql);
			$this->set('fIDArray', $this->fIDArray);	
		}
		
		function loadMultiFileSelector(){
			$html = Loader::helper('html');			
			$mps = Loader::helper('form/multi_file_selector', 'multi_file_selector');
			$mps->addHeaderAssets($this);
			$this->set('mps', $mps);	
		}
		
		function add(){
			$this->loadCollectionIDArray();
			$this->loadExistingLabels();
			$this->loadMultiFileSelector();
		}
		function edit(){
			$this->loadCollectionIDArray();
			$this->loadExistingLabels();
			$this->loadMultiFileSelector();	
		}
		function view(){
			$this->loadCollectionIDArray();	
		}
		
		
		function validate($data){
			$e = Loader::helper('validation/error');
			
			if(count($data['fIDArray']) < 1){
				$e->add(t('Select one or more files.'));
			}
			
			if(empty($data['label'])){
				$e->add(t('Enter a label that describes the references.'));
			}
			//$e->add($this->pre($data, TRUE));
			return $e;
		}
		
		function save($data){
			$db = Loader::db();
			$pos = 0;
			
			$txt = Loader::helper('text');
			$data['label'] = $txt->sanitizeFileSystem(preg_replace("/\s+/", '_', trim($data['label'])));			
			
			//Delete existing pages -- what does this do to versioning?
			$db->query('DELETE FROM btMultiFileSelectorItem WHERE bID='.intval($this->bID));
			
			//Add the pages
			foreach($data['fIDArray'] as $fID){ 
				$vals = array(intval($this->bID),intval($fID), $pos);
				$db->query("INSERT INTO btMultiFileSelectorItem (bID,fID,position) values (?,?,?)",$vals);
				$pos++;
			}
			
			parent::save($data);
		}
		
		
		function delete(){
			$db = Loader::db();
			$db->query("DELETE FROM btMultiFileSelectorItem WHERE bID=".intval($this->bID));		
			parent::delete();
		}
		
		
		
		function pre($thing, $save=FALSE){
			$str = '<pre style="white-space:pre; border:1px solid #ccc; padding:8px; margin:0 0 8px 0;">'.print_r($thing, TRUE).'</pre>';
			if(!$save){
				echo $str;	
			}
			return $str;
		}
		
	}
	
?>