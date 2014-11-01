<?php
function array2select($data, $name, $textName, $value=0, $isDisabled = FALSE, $optionValue='id')
{
	if($isDisabled)
		$isDisabled= 'disabled="disabled"';
	else 
		$isDisabled = '';
	$html = "<select name='$name' $isDisabled><option value=''>请选择</option>";
	foreach ($data as $k => $v)
	{
		if($value && $v[$optionValue] == $value)
			$select = 'selected="selected"';
		else 
			$select = '';
		$html .= "<option $select value='{$v[$optionValue]}'>{$v[$textName]}</option>";
	}
	$html .= '</select>';
	return $html;
}