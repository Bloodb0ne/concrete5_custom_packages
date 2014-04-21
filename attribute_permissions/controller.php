<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));



class AttributePermissionsPackage extends Package {

	protected $pkgHandle = 'attribute_permissions';
	protected $appVersionRequired = '5.6';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Contains a interface for editing attribute permission.");
	}
	
	public function getPackageName() {
		return t("Attribute Permission Interface");
	}
	public function uninstall(){

		parent::uninstall();
		
		$db = Loader::db();
        $db->Execute('DROP TABLE attributePermissions');
	}
	public function install() {
		$pkg = parent::install();	
		$def = SinglePage::add('/dashboard/attribute_permissions', $pkg);
		if(is_object($def)){
			
			$def->update(array('cName' => t('Attribute Permissions'), 
        	'cDescription' => t('Page for Administering Attribute permissions.')));
		}
        

	}
}

?>