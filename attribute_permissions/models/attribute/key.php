<?php 


class AttributeKey extends Concrete5_Model_AttributeKey{

	public function render($view = 'view', $value = false, $return = false) {

		Loader::model('attribute_permissions','attribute_permissions');
		
		$u = new User(); 
		$groups = $u->getUserGroups();
		$groups = implode(',',array_keys($groups));
		$test = AttributePermissions::getPermissionUser($this->getAttributeKeyID(),
												$groups,'view');
		

		if($test){

			$at = AttributeType::getByHandle($this->atHandle);
			$resp = $at->render($view, $this, $value, $return);
			if ($return) {
				return $resp;
			}

		}else{
			
			$name = $this->getAttributeKeyName();
			$id = $this->getAttributeKeyID();
			if($return)
				return "<span class='alert alert-error' style='padding:3px;'>You dont have permission to view attribute {$name}.</span>";

		}
	}

	public function update($args) {

		Loader::model('attribute_permissions','attribute_permissions');
		
		$u = new User(); 
		$groups = $u->getUserGroups();
		$groups = implode(',',array_keys($groups));
		$test = AttributePermissions::getPermissionUser($this->getAttributeKeyID(),
												$groups,'edit');
		

		if($test){
			$prevHandle = $this->getAttributeKeyHandle();

			extract($args);

			if (!$akIsSearchable) {
				$akIsSearchable = 0;
			}
			if (!$akIsSearchableIndexed) {
				$akIsSearchableIndexed = 0;
			}
			$db = Loader::db();

			$akCategoryHandle = $db->GetOne("select akCategoryHandle from AttributeKeyCategories inner join AttributeKeys on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akID = ?", $this->getAttributeKeyID());
			$a = array($akHandle, $akName, $akIsSearchable, $akIsSearchableIndexed, $this->getAttributeKeyID());
			$r = $db->query("update AttributeKeys set akHandle = ?, akName = ?, akIsSearchable = ?, akIsSearchableIndexed = ? where akID = ?", $a);
			
			$category = AttributeKeyCategory::getByID($this->akCategoryID);
			switch($category->allowAttributeSets()) {
				case AttributeKeyCategory::ASET_ALLOW_SINGLE:
					if ($asID > 0) {
						$as = AttributeSet::getByID($asID);
						if ((!$this->inAttributeSet($as)) && is_object($as)) {
							$this->clearAttributeSets();
							$this->setAttributeSet($as);
						}
					} else {
						// clear set
						$this->clearAttributeSets();
					}
					break;
			}

			
			if ($r) {
				$txt = Loader::helper('text');
				$className = $txt->camelcase($akCategoryHandle) . 'AttributeKey';
				$ak = new $className();
				$ak->load($this->getAttributeKeyID());
				$at = $ak->getAttributeType();
				$cnt = $at->getController();
				$cnt->setAttributeKey($ak);
				$cnt->saveKey($args);
				$ak->updateSearchIndex($prevHandle);
				return $ak;
			}

		}else{
			//Error message
		}
		
	}

	public function delete() {

		Loader::model('attribute_permissions','attribute_permissions');
		
		$u = new User(); 
		$groups = $u->getUserGroups();
		$groups = implode(',',array_keys($groups));
		$test = AttributePermissions::getPermissionUser($this->getAttributeKeyID(),
												$groups,'edit');
		

		if($test){

			$at = $this->getAttributeType();
			$at->controller->setAttributeKey($this);
			$at->controller->deleteKey();
			$cnt = $this->getController();
			
			$db = Loader::db();
			$db->Execute('delete from AttributeKeys where akID = ?', array($this->getAttributeKeyID()));
			$db->Execute('delete from AttributeSetKeys where akID = ?', array($this->getAttributeKeyID()));

			if ($this->getIndexedSearchTable()) {
				$columns = $db->MetaColumns($this->getIndexedSearchTable());
				$dba = NewDataDictionary($db, DB_TYPE);
				
				$fields = array();
				if (!is_array($cnt->getSearchIndexFieldDefinition())) {
					$dropColumns[] = 'ak_' . $this->akHandle;
				} else {
					foreach($cnt->getSearchIndexFieldDefinition() as $col => $def) {
						$dropColumns[] = 'ak_' . $this->akHandle . '_' . $col;
					}
				}
				
				foreach($dropColumns as $dc) {
					if ($columns[strtoupper($dc)]) {
						$q = $dba->DropColumnSQL($this->getIndexedSearchTable(), $dc);
						$db->Execute($q[0]);
					}
				}
			}

		}else{

			//Show error message 
		}
		
	}

	protected function saveAttribute($attributeValue, $passedValue = false) {

		Loader::model('attribute_permissions','attribute_permissions');
		
		$u = new User(); 
		$groups = $u->getUserGroups();
		$groups = implode(',',array_keys($groups));
		$test = AttributePermissions::getPermissionUser($this->getAttributeKeyID(),
												$groups,'edit');
		

		if($test){

			$at = $this->getAttributeType();
			$at->controller->setAttributeKey($this);
			$at->controller->setAttributeValue($attributeValue);
			if ($passedValue) {
				$at->controller->saveValue($passedValue);
			} else {
				$at->controller->saveForm($at->controller->post());
			}
			$at->__destruct();
			unset($at);

		}else{
			
			//Show proper error message 
		}
	}

}

?>