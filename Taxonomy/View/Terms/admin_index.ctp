<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
	->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
?>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'New Term'),
		array('action' => 'add', $vocabulary['Vocabulary']['id'])
	);
?>
<?php $this->end(); ?>

<?php
	if (isset($this->params['named'])) {
		foreach ($this->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}
?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Slug'),
		__d('croogo', 'Actions'),
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php
	$rows = array();

	// Default Content Type
	if(isset($vocabulary['Type'][0])){
		$defaultType = $vocabulary['Type'][0];
	}
	if(isset($this->params->query['type_id'])){
		if(isset($vocabulary['Type'][$this->params->query['type_id']])){
			$defaultType = $vocabulary['Type'][$this->params->query['type_id']];
		}
	}

	foreach ($termsTree as $id => $title):
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup',	$id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete',	$id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		// Title Column
		$titleCol = $title;
		if(isset($defaultType['alias'])){
			$titleCol = $this->Html->link($title,array(
			'plugin'=>'nodes',
			'controller'=>'nodes',
			'action'=>'term',
			'type'=>$defaultType['alias'],
			'slug'=>$terms[$id]['slug'],
			'admin'=>0));
		}

		// Build link list
		$typeLinks = "";
		if(count($vocabulary['Type']) > 1){
			$typeLinks = "(";
			foreach($vocabulary['Type'] as $type){
			$typeLinks .= $this->Html->link($type['title'],array(
				'admin'=>false,
				'plugin'=>'nodes',
				'controller'=>'nodes',
				'action'=>'term',
				'type'=>$type['alias'],
				'slug'=>$terms[$id]['slug']))." ";
			}
			$typeinks = ")";
		}
		$titleCol .= " <small>".$typeLinks."</small>";

		$rows[] = array(
			'',
			$id,
			$titleCol,
			$terms[$id]['slug'],
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

?>
</table>
