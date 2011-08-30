<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Provides a helper that creates a field for selecting multiple files. Also provides a block type and attribute type that employs the helper.
 * @package Section Title
 * @author Stephen Rushing
 * @category Packages
 * @copyright  Copyright (c) 2011 Stephen Rushing. (http://www.esiteful.com)
 */
class MultiFileSelectorPackage extends Package {

	protected $pkgHandle = 'multi_file_selector';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '0.9';
	
	public function getPackageDescription() {
		return t("Provides a helper that creates a field for selecting multiple files. Also provides a block type and attribute type that employs the helper.");
	}
	
	public function getPackageName() {
		return t("Multi-File Selector");
	}
	
	public function install() {
		$pkg = parent::install();
		
		BlockType::installBlockTypeFromPackage('multi_file_selector', $pkg);
		AttributeType::add('multi_file_selector', t('Multi-File Selector'), $pkg);	
	}
	
	public function upgrade(){
		parent::upgrade();
		
			
			
	}
}