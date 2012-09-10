<?php 
foreach($fields as $field => $options){ 
	if( 'richtext' == $options['type']){ 
		$id = $type.'_'.$field; $name =  $type.'['.$field.']' ; $html = ''; ?>

		<h2><?php echo $options['label']; ?></h3>
		<?php if(isset($options['description'])) description($options['description']) ?>
		<?php wp_editor($object->$field, $id, array('textarea_name' => $name, 'teeny' => true, 'media_buttons' => false)) ?>
		
		<?php unset($fields[$field]);
	}
}
?>
<?php if (!empty($fields)): ?>
	<table class='form-table'>
		<tbody>
			<?php foreach ($fields as $field => $options) { 
					if($options['hidden'] == true) continue;
					$id = $type.'_'.$field; $name =  $type.'['.$field.']' ; $html = '';
					if(!isset($options['size'])){
						$size = $options['type'] == 'integer' ? 5 : 15;
					} else {
						$size = $options['size'] ;
					};
					if(isset($options['html'])){
						foreach ($options['html'] as $k => $v) {
							$html .= "$k=\"$v\" ";
						}
					}
					?>
				<tr>
					<th>
						<?php label($options['label'], $id  ) ?>
						<?php if($options['type'] == 'text_area'){ description($options['description']); } ?>
					</th>
					<td>
						<?php switch ($options['type']) {

							case 'date': ?>
								<input type="text" <?php html_attributes( array( 'name' => $name, 'id' => $id, 'value' => $object->$field, 
								'size' => 10, 'class' => 'date' )) ?> <?php echo $html ?>>
								<?php description($options['description']) ?>
							<?php break;
							
							case 'post_type':
								$posts = get_posts(array('post_type' => $options['post_type']));?>
								<select <?php html_attributes(array('name' => $name, 'id' => $id, 'class' => 'text')) ?>  <?php echo $html ?>>
								<?php foreach ($posts as $post) {?>
									<option value="<?php echo $post->ID ?>" <?php echo $object->$field == $post->ID ? ' selected' : ''?> >
										<?php echo $post->post_title ?>
									</option>
								<?php } ?>
								</select> 
								<?php description($options['description']) ?>
							<?php break;

							case 'set':?>
								<select <?php html_attributes(array('name' => $name, 'id' => $id, 'class' => 'text')) ?>  <?php echo $html ?>>
								<?php foreach ($options['values'] as $value => $label) {?>
									<option value="<?php echo $value ?>" <?php echo $object->$field == $value ? ' selected' : ''?> >
										<?php echo $label ?>
									</option>
								<?php } ?>
								</select> 
								<?php description($options['description']) ?>
							<?php break;

							case 'text_area': ?>
								<textarea <?php html_attributes( array( 
								'name' => $name, 'id' => $id, 'class' => 'text', 'cols' => 50, 'rows' => 3  
								)) ?><?php echo $html ?>><?php echo $object->$field ?></textarea>
							<?php break;

							default: ?>
								<input type="text" <?php html_attributes( array( 
								'name' => $name, 'id' => $id, 'value' => $object->$field, 'class' => 'text', 'size' => $size 
								)) ?> <?php echo $html ?>> 
								<?php description($options['description']) ?>
							<?php break;

						} ?>
					</td>
			<?php } ?>
		</tbody>
	</table>
<?php endif ?>
<?php if(isset($custom_single)){?>
	<input type="hidden" name="custom_single" value="<?php $custom_single = explode('\\', $custom_single); echo $custom_single[sizeof($custom_single)-1] ?>">
<?php } ?>
