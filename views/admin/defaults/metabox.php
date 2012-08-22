<?php foreach($fields as $field => $options){ 
	if( 'richtext' == $options['type']){ 
		$id = $type.'_'.$field; $name =  $type.'['.$field.']' ; $html = ''; ?>

		<h2><?php echo $options['label']; ?></h3>
		<?php if(isset($options['description'])) description($options['description']) ?>
		<?php wp_editor($object->$field, $id, array('textarea_name' => $name, 'teeny' => true, 'media_buttons' => false)) ?>
		
		<?php unset($fields[$field]);
	}
}?>

<table class='form-table'>
	<tbody>
		<?php foreach ($fields as $field => $options) { 
				if($options['hidden'] == true) continue;
				$id = $type.'_'.$field; $name =  $type.'['.$field.']' ; $html = '';
				if(isset($options['html'])){
					foreach ($options['html'] as $k => $v) {
						$html .= "$k=\"$v\" ";
					}
				}
				?>
			<tr>
				<th>
					<?php label($options['label'], $id  ) ?>
				</th>
				<td>
					<?php switch ($options['type']) {
						case 'date': ?>
							<input type="text" <?php html_attributes( array( 'name' => $name, 'id' => $id, 'value' => $object->$field, 
							'size' => 10, 'class' => 'date' )) ?> <?php echo $html ?>>
						<?php break;
						
						default: ?>
							<input type="text" <?php html_attributes( array( 'name' => $name, 'id' => $id, 'value' => $object->$field, 'class' => 'text' )) ?> <?php echo $html ?>> 
						<?php break;
					} ?>
					<br/><?php description($options['description']) ?>
				</td>
		<?php } ?>
	</tbody>
</table>
