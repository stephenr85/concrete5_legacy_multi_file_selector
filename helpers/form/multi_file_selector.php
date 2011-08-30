<?php

class FormMultiFileSelectorHelper {
	
	private $view;
	private $html;
	
	function __construct(){
		$this->view = View::getInstance();			
		$this->html = Loader::helper('html');
		$this->urls = Loader::helper('concrete/urls');
		$this->cssToolsUrl = $this->urls->getToolsURL('css', 'multi_page_selector');
	}

	public function addHeaderAssets($to=NULL){
		if(is_null($to)){
			$to = $this->view;
		}
		$to->addHeaderItem($this->html->javascript('jquery.ui.js'));
		$to->addHeaderItem($this->html->javascript('ccm_multiFileSelector.js', 'multi_file_selector'));	
		$to->addHeaderItem($this->html->css('multi-file-selector.css', 'multi_file_selector'));
		
		$to->addHeaderItem($this->getTooledCss('multi-file-selector.css'), 'multi_page_selector');	
	}
	
	public function getTooledCss($file){
		$css = $this->html->css($file, 'multi_page_selector');
		$css = $this->html->css($this->cssToolsUrl.'?file='.preg_replace('/\?.+$/', '', str_replace(BASE_URL ,'', $css->href)));
		return $css;
	}
	
	public function create($name, $values=NULL, $attrs=NULL, $append=NULL){
		$out = '';
		
		if(is_string($values)){
			$values = explode(',',$values);
		}
		$form = Loader::helper('form');
		$out = '<ul class="items">';
		//Create the item template
		$input = $form->hidden($name, -1);
		$out .= self::_item($input, NULL, array('class'=>'template'));
		
		//Add existing items
		if(is_array($values)){
			
			foreach($values as $value){
				$file = File::getById($value);
				$input = $form->hidden($name, $value);
				$out .= self::_item($input, $file->getTitle());
			}			
		}
		$out .= '</ul>';		
		
		//Create the attributes string for the wrapper		
		$wrapAttrDefArr = $wrapAttrArr = array(
			'class'=>'ccm-multi-file-selector'
		);
		if(is_array($attrs)){
			$wrapAttrArr = array_merge($wrapAttrDefArr, $attrs);
		}
		
		foreach($wrapAttrArr as $attr=>$val){
			if(($wrapAttr == 'class') && strpos($val, $wrapAttrDefArr[$attr])===FALSE){
				$val .= $wrapAttrDefArr[$attr];
			}
			$wrapAttrs.= "$attr=\"$val\" ";
		}
		
		//Auto append a page selector and jquery plugin instantiatior, if nothing else is sent
		if(is_null($append)){
			$append = "<a class=\"add\">".t('Choose file')."</a>";
			$jQuerySelector = isset($wrapAttrArr['id']) ? 'div#'.$wrapAttrArr['id'] : 'div.'.preg_replace("/\s+/", '.', $wrapAttrArr['class']);
			
			$append .= "<script type=\"text/javascript\">$(function(){ $(\"$jQuerySelector\").ccm_multiFileSelector() });</script>";
		}
		
		//Wrap the output
		$out = "<div $wrapAttrs>$out$append</div>";
		
		//Try adding the header assets
		$this->addHeaderAssets();
		
		return $out;
	}
	
	
	
	private function _item($input, $name=NULL, $attrArr=NULL){
		$attrs = '';
		if(is_array($attrArr)){
			foreach($attrArr as $attr=>$val){
				$val = $attrArr[$attr];
				$attrs.= "$attr=\"$val\" ";
			}
		}
		$txtRemove = t('Remove');
		$txtDetails = t('Details');
		$act = "<span class=\"actions\"><a class=\"details\" title=\"$txtDetails\">$txtDetails</a><a class=\"remove\" title=\"$txtRemove\">$txtRemove</a></span>";
		return "<li $attrs><span class=\"icon\"></span>$input<span class=\"name\">$name</span>$act</li>";
	}
	
	
}