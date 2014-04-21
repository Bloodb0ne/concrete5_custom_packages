<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class AttributePermissions{

	public function getAttributes(){
		$db= Loader::db();
		$results = $db->GetAll("select akID,akHandle,akName from AttributeKeys");

		return $results;
	}

	public static function getPermissions($attrID,$groupID){
		$db= Loader::db();
		$results = $db->GetAll("select viewPerm,editPerm,deletePerm from attributePermissions WHERE akID=? AND groupId=?",
								array($attrID,$groupID));


		return $results;
	}

	public function getEntries(){
		$db= Loader::db();
		$results = $db->GetAll("select akHandle,aP.akID,aP.groupID,aP.permID,aP.viewPerm,aP.editPerm,aP.deletePerm from attributePermissions as aP JOIN AttributeKeys ON 
AttributeKeys.akID=aP.akID");
		for ($i=0; $i < count($results); $i++) { 
			$val = $results[$i]['viewPerm']==1?'checked':'';
			$results[$i]['viewPerm'] = $val;

			$val = $results[$i]['editPerm']==1?'checked':'';
			$results[$i]['editPerm'] = $val;

			$val = $results[$i]['deletePerm']==1?'checked':'';
			$results[$i]['deletePerm'] = $val;
		}
		return $results;
	}

	public function insertEntries($data){
		$db = Loader::db();
		$cnt = 0;
		foreach ($data as $entry) {
			
			$test = $db->Execute(
			  'INSERT INTO attributePermissions VALUES(?, ?, ?, ?, ?, ?)',
			  array(
			  	$entry['permID'],
			  	$entry['groupID'],
			    $entry['akID'],
			    $entry['viewPerm'],
			    $entry['editPerm'],
			    $entry['deletePerm']
			  )
			);
			if($test) $cnt++;
		}

		return $cnt;
	}

	public function updateEntries($data){
		$db = Loader::db();
		$cnt = 0;
		foreach ($data as $entry) {
			
			$test = $db->Execute(
			  'REPLACE INTO attributePermissions VALUES(?, ?, ?, ?, ?, ?)',
			  array(
			  	$entry['permID'],
			  	$entry['groupID'],
			    $entry['akID'],
			    $entry['viewPerm'],
			    $entry['editPerm'],
			    $entry['deletePerm']
			  )
			);
			if($test) $cnt++;
		}
		return $cnt;
	}

	public function deleteEntries($data){
		if(is_array($data) && count($data) > 0){
			$db = Loader::db();
			$ids = implode(',',$data);

			$rs = $db->Execute(
			  'DELETE FROM attributePermissions WHERE permID IN (?)',
			  $ids
			);
			return $db->Affected_Rows();
			
		}else{

			return 0;
		}
	}
}

?>