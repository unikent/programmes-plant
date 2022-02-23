<?php
/**
 *
 *
 */
class Definitions_Task
{

	/**
	 * Generate Site Editor compatible definition json from the programmes plant sections and fields
	 *
	 * @param array  $arguments The arguments sent to the command. Optional maxWidth
	 */
	public function run($arguments = array())
	{
		if (sizeof($arguments) === 0) {
			$level = 'ug';
		} else {
			$level = $arguments[0];
		}
		$sql = "SELECT 
s.name,
f.*
FROM programmesections_$level s
JOIN programmes_fields_$level f
ON f.section = s.id

ORDER BY s.`order`,f.`order`";
		$data = DB::query($sql);
		$definition = [
			'label' => sprintf('%s Programme', strtoupper($level)),
			'name' => sprintf('%s-programme', $level),
			'info' => sprintf('A %s programme running', $level),
			'version' => 1,
			'fields' => [],
		];
		$sectionName = null;
		$section = [];
		foreach ($data as $d) {
			if ($d->name != $sectionName) {
				if ($section) {
					$definition['fields'][] = $section;
				}
				$sectionName = $d->name;
				$section = [
					'name' => $this->slugify($d->name),
					'label' => $d->name,
					'type' => 'group',
					'fields' => [],
					'info' => '',
				];
			}
			$section['fields'][] = $this->createFieldDefinition($d);
		}
		if ($section) {
			$definition['fields'][] = $section;
		}
		echo json_encode($definition);
	}

	private function createFieldDefinition($d)
	{
		$field = [
			'name' => $this->slugify($d->field_name),
			'label' => $d->field_name,
			'info' => $d->field_description,
		];
		switch ($d->field_type) {
			case 'text':
				$field['type'] = 'text';
				break;
			case 'textarea':
				$field['type'] = 'richtext';
				break;
			case 'checkbox':
			case 'select':
				if ($d->field_meta) {
					$field['type'] = $d->field_type == 'checkbox' ? 'multiselect' : 'select';
					$field['options'] = [];
					foreach (explode(',', $d->field_meta) as $option) {
						$option = trim($option);
						if ($option) {
							$field['options'][] = [
								'value' => $option,
								'label' => ucwords($option),
							];
						}
					}
				}
				else {
					$field['type'] = 'switch';
				}
				break;
			case 'table_select':
				$field['type'] = 'select';
				break;
			case 'table_multiselect':
				$field['type'] = 'multiselect';
				$field['multiple'] = true;
				break;
			case 'image':
				$field['type'] = 'image';
				break;
			case 'file':
				$field['type'] = 'file';
				break;
		}
		return $field;
	}

	private function slugify($name)
	{
		return preg_replace('/[^a-z0-9_]/', '_', strtolower($name));
	}
}
