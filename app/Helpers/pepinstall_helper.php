<?php

function _mnu($uri, $mnu_path)
{

	$tot_splits = substr_count($mnu_path, '/');

	if ($tot_splits == 0 && $uri->getTotalSegments() > 0) {
		if ($mnu_path == $uri->getSegment(1)) echo ' open active ';
	}
	if ($tot_splits == 1 && $uri->getTotalSegments() > 1) {
		if ($mnu_path == $uri->getSegment(1) . '/' . $uri->getSegment(2)) echo ' open active';
	}
	if ($tot_splits == 2 && $uri->getTotalSegments() > 2) {
		if ($mnu_path == $uri->getSegment(1) . '/' . $uri->getSegment(2) . '/' . $uri->getSegment(3)) echo ' open active';
	}
	if ($tot_splits == 3 && $uri->getTotalSegments() > 3) {
		if ($mnu_path == $uri->getSegment(1) . '/' . $uri->getSegment(2) . '/' . $uri->getSegment(3) . '/' . $uri->getSegment(4)) echo ' open active';
	}        

}


function _mnu_sub_nav($array, $value)
{

	if ($array === NULL) return;

	if (is_string($array)) {
		if ($array == $value) echo ' active ';
	}

	if (is_array($array) && isset($array['header_nav'])) {
		if ($array['header_nav'] == $value) echo ' active ';
	}
}


function is_selected($list, $value)
{

	if ($list === NULL) return true;

	if (is_array($list)) {
		if (in_array($value, $list)) {
			return true;
		}
	} else {
		if ($list == $value) return true;
	}

	return false;
}

function gen_select_dropdown($db, $sql, $selected_val)
{

    $query = $db->query($sql);
    foreach ($query->getResult() as $row) { 

        ?>
            <option <?php if ($selected_val == $row->value) echo 'selected'?> value="<?php echo $row->value?>"><?php echo $row->description?></option>
        <?php

    }

}

function print_pre ($expression, $return = false, $wrap = false){
	$css = 'border:1px dashed #06f;background:#ddd;padding:1em;text-align:left;';
	if ($wrap) {
		$str = '<p style="' . $css . '"><tt>' . str_replace(
			array('  ', "\n"), array('&nbsp; ', '<br />'),
			htmlspecialchars(print_r($expression, true))
		) . '</tt></p>';
	} else {
		$str = '<pre style="' . $css . '">'
		. htmlspecialchars(print_r($expression, true)) . '</pre>';
	}
	if ($return) {
	  if (is_string($return) && $fh = fopen($return, 'a')) {
			fwrite($fh, $str);
			fclose($fh);
	  }
	  return $str;
	} else
	  echo $str;
}	

  function replaceDirectionWithValue($latlong){
	$directionChar = substr($latlong,0,1);
	switch ($directionChar) {
		case 'N':
		case 'E':
		case 'n':
		case 'e':
			return substr($latlong,1);
		case 'S':
		case 'W':
		case 's':
		case 'w':
			return '-'.substr($latlong,1);
		default:
			return $latlong;
	}
}

function assignDirectionChar($latlong,$isLat){
	if($isLat){
		if($latlong >= 0) return 'N'.$latlong;
		return 'S'.substr($latlong,1);
	} 
	else{
		if($latlong >= 0) return 'E'.$latlong;
		return 'W'.substr($latlong,1);
	} 

}
