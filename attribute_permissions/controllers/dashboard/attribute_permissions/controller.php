<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardAttributePermissionsController extends Controller {


	public function __construct() { 
		
	}
	private function getCheckValue($chk){
		return $chk=='on'?'1':'0';
	}
	public function view(){
		//Database::setDebug(true);
		Loader::model('attribute_permissions','attribute_permissions');
		$model = new AttributePermissions();
		if ($this->isPost()) {
			
			

			//Fetch values
			$insert = array();
			$update = array();
			if(isset($_POST['permGroup']))
			{
				foreach ($_POST['permGroup'] as $key => $value) {
					$entry = array();
					$entry['permID'] = $_POST['permID'][$key];
					$entry['groupID'] = $_POST['permGroup'][$key];
					$entry['akID'] = $_POST['attrId'][$key];
					$entry['viewPerm'] = $this->getCheckValue($_POST['viewPerm'][$key]);
					$entry['editPerm'] = $this->getCheckValue($_POST['editPerm'][$key]);
					$entry['deletePerm'] = $this->getCheckValue($_POST['deletePerm'][$key]);
					
					if($entry['permID'] == '')
					{
						$insert[] = $entry;

					}else{

						$update[] = $entry;
					}
				}
			}
			
			$cnt = count($insert);
			//Do we have insert error
			$error = 0;
			$insert = array_udiff($insert,$update,function($a,$b){
				$test = $a['groupID'] == $b['groupID']&&$a['akID'] == $b['akID'];
				return !$test;
			});

			if(count($insert) != $cnt){
				$error = 1;
			}

			$deleted = array();
			if(isset($_POST['deleted']))
			{
				foreach ($_POST['deleted'] as $key => $value) {
					$deleted[] = $key;
				}
				
			}
			
			$insCount = $model->insertEntries($insert);
			$updCount = $model->updateEntries($update);
			$delCount = $model->deleteEntries($deleted);

			if($error == 0)
			{
				$this->set('success','Inserted: '.$insCount.' Updated: '.$updCount.' Deleted: '.$delCount);
			}else
			{
				$this->set('error','Failed to create entries.');
			}
				
		}
		//$this->set('success',false);
		
		$entries = $model->getEntries();
		$attrs = $model->getAttributes();

		Loader::model('User');
		$u = new User();
		$groups = $u->getUserGroups();

		$this->set('entries',$entries);
		$this->set('attributes',$attrs);
		$this->set('groups',$groups);
	}

}

?>