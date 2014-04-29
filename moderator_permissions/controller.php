<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));


class ModeratorPermissionsPackage extends Package {

	protected $pkgHandle = 'moderator_permissions';
	protected $appVersionRequired = '5.6';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Adds a group and sets permission to the dashboard for it");
	}
	
	public function getPackageName() {
		return t("Moderator Group Package");
	}

	private function removePermissions($group,$pages){
		foreach ($pages as $page) {
			$pageObj = Page::getByPath($page['path']);

			$pe = GroupPermissionAccessEntity::getOrCreate($group);

			if(isset($page['action']) && 
			$page['action'] == 'exclude'){

				$permAction = PagePermissionKey::ACCESS_TYPE_EXCLUDE;

			}else{

				$permAction = PagePermissionKey::ACCESS_TYPE_INCLUDE;
			}

			foreach($page['permissions'] as $pkHandle) { 
				$pk = PagePermissionKey::getByHandle($pkHandle);
				$pk->setPermissionObject($pageObj);
				$pa = $pk->getPermissionAccessObject();
				if (is_object($pa)) {
					$pa->removeListItem($pe, false, $pageAction);
				}
			}
			
		}
	}
	private function setPermissions($group,$pages,$remove=false){
		foreach ($pages as $page) {
			$pageObj = Page::getByPath($page['path']);
			
			if(isset($page['action']) && 
				$page['action'] == 'exclude'){

				$permAction = PagePermissionKey::ACCESS_TYPE_EXCLUDE;

			}else{

				$permAction = PagePermissionKey::ACCESS_TYPE_INCLUDE;
			}
			$pageObj->assignPermissions($group,
										$page['permissions'],
										$permAction);
		}
	}

	public function uninstall(){
		
		//Get Group 
		$modgroup = Group::getByName('Moderators');

		$pagesPerm = array(
			array('path'=>'/dashboard','permissions'=>array('view_page')));
		//Remove permissions
		if(is_object($modgroup)) {
			$this->removePermissions($modgroup,$pagesPerm);
			$modgroup->delete();
		}
		parent::uninstall();
	}

	
	public function install() {
		$pkg = parent::install();	

		//Create moderator group
		$modgroup = Group::getByName('Moderators');
		if(!is_object($modgroup)) {
        	$modgroup = Group::add('Moderators', 'Group that edits site content');
		}

		//Permission arrray
		$pagesPerm = array(
			array('path'=>'/dashboard','permissions'=>array('view_page')));
		$this->setPermissions($modgroup,$pagesPerm);
	}
}

?>