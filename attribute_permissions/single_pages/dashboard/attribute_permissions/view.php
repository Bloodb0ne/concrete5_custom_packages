<?php
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('concrete/interface');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Attribute Permissions'), "", false, false); ?>
<style type="text/css">
	#ref-entry{
		display: none;
	}
	div.removed{
		opacity: 0.5;
	}
	#header-list{
		width: 100%;
	}
	.clearfix{
		clear: both;
	}
	#header-list{
		border-bottom: 1px solid #eee;
		margin-bottom: 10px;
	}
	#header-list > span{
		font-weight: bold;
		display: block;
		float:left;
		margin-right: 8px;
	}
	.real-entry{
		margin-bottom: 10px;
	}
	.real-entry > input.size10{
		margin-left: 25px;
		margin-right: 25px;
	}
	.size10{
		width: 60px;
	}
	.size20, .real-entry > .size20{
		width: 300px;
	}
	.size40{
		width: 250px;
	}
</style>
<script type="text/javascript">
	function addEntry(){
	 
	 var ref = $('#ref-entry').clone();
	 ref.attr('id','');
	 ref.addClass('real-entry');
	 //Get Numbber
	 var num = $('div.real-entry').length;
	 
	 var children = ref.children();
	 children.each(function(ind,val){
	 	
	 	var name = $(val).attr('name')
	 	if(typeof name != 'undefined'){
	 		$(val).attr('name',name+'['+num+']');
	 	}
	 })

	 $('#permission-form > .ccm-pane-body').append(ref);
		
	}
	
	function removeEntry(el){
		$(el).parent().addClass('removed');
		var children = $(el).parent().children();
	 	children.each(function(ind,val){
	 	
	 	var name = $(val).attr('name')
	 	if(typeof name != 'undefined'){
	 		$(val).attr('disabled','disabled');
	 	}
	 })

	 var delID = $(el).parent().find('input[name^="permID"]').val();
	 var deleteArg = $('<input>');
	 deleteArg.attr('type','hidden');
	 deleteArg.attr('name','deleted['+delID+']');
	 
	 $(el).parent().append(deleteArg);
	}

</script>

 	<div id='ref-entry'>
 			<input type='hidden' name='permID'>
 			<select class='size20' name='permGroup'>
 				<?php foreach($groups as $id => $name): ?>
 					<option value='<?php echo $id; ?>'><?php echo $name; ?></option>
 				<?php endforeach; ?>
 			</select>
 			<select class='size20' name='attrId'>
 				<?php foreach($attributes as $attr): ?>
 					<option value='<?php echo $attr['akID']; ?>'><?php echo $attr['akName']; ?></option>
 				<?php endforeach; ?>
 			</select>
 			
 			<input class='size10' type='checkbox' name='viewPerm'>
 			<input class='size10' type='checkbox' name='editPerm'>
 			<input class='size10' type='checkbox' name='deletePerm'>
 			<input type="button" class="btn ccm-button-v2 error ccm-button-v2-right" value="Delete" onclick="removeEntry(this);">
 	</div>
 	

 	<form action="<?=$this->action('view')?>" method="post" id='permission-form'>
 	<div class="ccm-pane-body">
 		<div id='header-list'>
	 		<span class='size20'>Group Name</span>
	 		<span class='size20'>Attribute Name</span>
	 		<span class='size10'>View</span>
	 		<span class='size10'>Edit</span>
	 		<span class='size10'>Delete</span>
	 		<div class="clearfix"></div>
 		</div>
 		<?php foreach($entries as $numId => $entry): ?>
 			<div class='real-entry'>
 			<input type='hidden' name='permID[<?php echo $numId ?>]' value='<?php echo $entry['permID']; ?>'>
 			<select class='size20' name='permGroup[<?php echo $numId ?>]'>
 				<?php foreach($groups as $id => $name):
 					$selected = '';
 					if($id == $entry['groupID']){
 						$selected = 'selected';
 					}
 				?>
 					<option <?php echo $selected; ?> value='<?php echo $id; ?>'><?php echo $name; ?></option>
 				<?php endforeach; ?>
 			</select>

 			<select class='size20' name='attrId[<?php echo $numId ?>]'>
 				<?php foreach($attributes as $attr):
 					$selected = '';
 					if($attr['akID'] == $entry['akID']){
 						$selected = 'selected';
 					}
 				?>
 					<option <?php echo $selected; ?> value='<?php echo $attr['akID']; ?>'><?php echo $attr['akName']; ?></option>
 				<?php endforeach; ?>
 			</select>
 			
 			<input class='size10' type='checkbox' 
 			name='viewPerm[<?php echo $numId ?>]' <?php echo $entry['viewPerm']; ?>>	
 			<input class='size10' type='checkbox' 
 			name='editPerm[<?php echo $numId ?>]' <?php echo $entry['editPerm']; ?>>
 			<input class='size10' type='checkbox' 
 			name='deletePerm[<?php echo $numId ?>]' <?php echo $entry['deletePerm']; ?>>
 			
 			<input type="button" class="btn ccm-button-v2 error ccm-button-v2-right" value="Delete" onclick="removeEntry(this);">
 		</div>
 		<?php endforeach; ?>

	</div>
 	<div class="ccm-pane-footer">
	 	<button type="submit" value="Save" class="btn primary ccm-button-right">Save <i class="icon-ok-sign icon-white"></i></button>
		<input class='btn' name='Add' type='button' value='Add Entry' onclick='addEntry();'>
 	</div>
   </form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>