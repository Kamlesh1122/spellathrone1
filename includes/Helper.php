<?php

include_once "Config.php";

function actionlist()
{

	return [

		'all' => ['title' => 'All'],

		'show' => ['title' => 'Show'],

		'view' => ['title' => 'View'],

		'add' => ['title' => 'Add'],

		'store' => ['title' => 'Store'],

		'edit' => ['title' => 'Edit'],

		'update' => ['title' => 'Update'],

		'delete' => ['title' => 'Delete'],

		'export' => ['title' => 'Export'],

		'bulkaction' => ['title' => 'Bulkaction'],

		'newsletter' => ['title' => 'newsletter'],

		'sendnewsletter' => ['title' => 'sendnewsletter'],

	];
}



function modulelsit()
{

	$final = [];

	$data = select('SELECT * FROM `admin_module`');

	foreach ($data as $key => $value) {

		$final[$value['slug']] = ['title' => $value['title'], 'action' => json_decode($value['action'], true)];
	}

	return $final;
}





function get_module_permission($slug)
{

	$data = selectOne('SELECT id,action FROM `admin_module` WHERE slug = "' . $slug . '"');

	return !empty($data['action']) ? json_decode($data['action'], true) : [];
}



function trim_slug($slug)
{

	$slug = trim($slug, '/');

	return !empty($slug) ? '/' . $slug : '';
}



function trim_dir_path($slug)
{

	$slug = str_replace('/', DS, $slug);

	$slug = trim($slug, DS);

	return !empty($slug) ? DS . $slug : '';
}



function base_url()
{

	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . trim_slug(SUBFOLDER);
}



function site_url($slug = '')
{

	return base_url() . trim_slug($slug);
}



function upload_preview($url)
{

	return asset('/images/' . getFilePreview($url));
}



function url($slug = '')
{

	return base_url() . trim_slug($slug);
}



function token()
{

	return $_SESSION['checkpoint_token'];
}



function token_field()
{

	return '<input type="hidden" name="checkpoint_token" value="' . token() . '" >';
}



function asset($slug = '')
{

	return site_url('/public/asset') . trim_slug($slug);
}



function getAllUrlParm()
{

	global $router;

	return $router->getMatch();
}

function getUrlParm($parm)
{

	global $router;

	$data = $router->getMatch();

	return !empty($data[$parm]) ? $data[$parm] : '';
}



function render($view, $data = [])
{
	//echo SITE_DIR.trim_dir_path($view);
	return \Core\View::render(trim_dir_path($view), $data);
}



function render_buffer($view, $data = [])
{

	return \Core\View::buffer_render(trim_dir_path($view), $data);
}



function render_first($views, $data = [])
{
	if (!empty($views) && is_array($views)) {
		foreach ($views as $key => $view) {
			$pt = SITE_DIR . "/App/Views" . trim_slug($view);
			//dd($pt);
			if (file_exists($pt)) {
				return \Core\View::render($view, $data);
			}
		}
	}
	return '';
}



function redirect($url)
{

	header('Location: ' . $url);
}



function dumpRoutes()
{

	global $router;

	return $router->getCollection();
}



function admin_url($slug = '')
{

	//echo $slug;

	return site_url(ADMIN_SLUG) . trim_slug($slug);
}



function admin_session()
{

	if (!empty($_SESSION['admin_id'])) {

		$_SESSION['admin'] = selectOne('SELECT * FROM `admins` WHERE id = ' . $_SESSION['admin_id']);

		if (empty($_SESSION['admin']['admin_role'])) {

			$_SESSION['admin_id'] = '';

			//redirect(admin_url('/'));

		}

		$_SESSION['admin']['role'] = selectOne("SELECT * FROM `admin_roles` WHERE slug = '" . $_SESSION['admin']['admin_role'] . "'");
	} else {

		$_SESSION['admin_id'] = '';

		//redirect(admin_url('/'));

	}
}



function shownotification()
{

	$session = !empty($_SESSION['flash']) ? $_SESSION['flash'] : [];

	$_SESSION['flash'] = [];

	if (!empty($session['success'])) {

		return ['type' => 'success', 'msg' => $session['success']];
	}

	if (!empty($session['error'])) {

		return ['type' => 'error', 'msg' => $session['error']];
	}

	if (!empty($session['warning'])) {

		return ['type' => 'warning', 'msg' => $session['warning']];
	}

	if (!empty($session['info'])) {

		return ['type' => 'info', 'msg' => $session['info']];
	}

	return [];
}



function get_admin_details()
{

	return !empty($_SESSION['admin']) ? $_SESSION['admin'] : [];
}



function flash($key, $value)
{

	$_SESSION['flash'][$key] = $value;
}



function admin_permission($module_slug = '')
{

	$final = [];

	if (!empty($_SESSION['admin']['role']['permission'])) {

		$permission = json_decode($_SESSION['admin']['role']['permission'], true);

		if (empty($module_slug)) {

			$all_permission = getTable('admin_module');

			foreach ($all_permission as $key => $value) {

				$data = json_decode($value['action'], true);

				if (!empty($permission[$value['slug']])) {

					$permission[$value['slug']] = array_intersect($permission[$value['slug']], $data);
				}
			}

			return $permission;
		} else {

			$data = get_module_permission($module_slug);

			if (!empty($permission[$module_slug]) && !empty($data)) {

				$final = array_intersect($permission[$module_slug], $data);
			}

			return $final;
		}
	}

	return $final;
}





function db()
{

	try {

		return new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}



function rjson($array)
{

	echo json_encode($array);

	exit();
}



function getTable($table, $cols = '*')
{

	try {

		$sth = db()->prepare("SELECT " . $cols . " FROM " . $table . " ");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}





function pluck($listtitle, $listkey, $query, $parm = [])
{

	$final = [];

	foreach (select($query, $parm) as $key => $value) {

		$final[$value[$listkey]] = $value[$listtitle];
	}

	return $final;
}



function select($query, $parm = [])
{

	try {

		$sth = db()->prepare($query);

		$sth->execute($parm);

		return $sth->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}







function delete($table, $id, $pk = 'id')
{

	try {

		$sth = db()->prepare('DELETE FROM ' . $table . ' WHERE ' . $pk . ' = ?');

		$sth->execute([$id]);

		return $sth->rowCount();
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}



function selectOne($query, $parm = [])
{

	try {

		$sth = db()->prepare($query);

		$sth->execute($parm);

		return $sth->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}



function find($table, $id, $pk = 'id')
{
	try {

		$sth = db()->prepare('select * from ' . $table . ' where ' . $pk . ' = ?');

		$sth->execute([$id]);

		return $sth->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}



function query($query, $parm = [])
{

	try {

		$sth = db()->prepare($query);

		$sth->execute($parm);

		return $sth->rowCount();
	} catch (PDOException $e) {

		print "Error!: " . $e->getMessage() . "<br/>";

		die();
	}
}



function insert($table, $data = [])
{

	try {

		$colslist = '';

		$valueslist = '';

		foreach ($data as $key => $value) {

			$colslist .= !empty($colslist) ? ', ' . $key . ' ' : $key;

			$valueslist .= !empty($valueslist) ? ',?' : '?';
		}



		$dbh = db();

		$stmt = $dbh->prepare("INSERT INTO " . $table . " (" . $colslist . ") VALUES(" . $valueslist . ")");

		try {

			$dbh->beginTransaction();

			$stmt->execute(array_values($data));

			$ids = $dbh->lastInsertId();

			$dbh->commit();

			return !empty($ids) ? true : false;
		} catch (PDOExecption $e) {

			$dbh->rollback();

			print "Error!: " . $e->getMessage() . "</br>";
		}
	} catch (PDOExecption $e) {

		print "Error!: " . $e->getMessage() . "</br>";
	}
}



function update($table, $data, $id, $pk = 'id')
{

	$colslist = '';

	foreach ($data as $key => $value) {

		$colslist .= !empty($colslist) ? ", " . $key . ' = ?' : " " . $key . ' = ?';
	}



	$stmt = "UPDATE " . $table . " SET " . $colslist . " WHERE " . $pk . " = ?";

	$tempata = !empty(array_values($data)) ? array_values($data) : [];

	array_push($tempata, $id);

	return query($stmt, $tempata);
}

function updateq($table, $data, $where = '', $parm = '')
{
	$colslist = '';
	foreach ($data as $key => $value) {
		$colslist .= !empty($colslist) ? ", " . $key . ' = ?' : " " . $key . ' = ?';
	}
	$where = !empty($where) ? $where : ' 1 = 1 ';
	$stmt = "UPDATE " . $table . " SET " . $colslist . " WHERE " . $where . " ";
	$tempata = !empty(array_values($data)) ? array_values($data) : [];
	return query($stmt, $tempata);
}



function updateQuery($table, $data, $where = '', $parm = [])
{

	try {

		$colslist = '';

		$valueslist = '';

		foreach ($data as $key => $value) {

			$colslist .= !empty($colslist) ? ', ' . $key . ' ' : $key;

			$valueslist .= !empty($valueslist) ? ',?' : '?';
		}



		$dbh = db();

		$stmt = $dbh->prepare("INSERT INTO " . $table . " (" . $colslist . ") VALUES(" . $valueslist . ")");

		try {

			$dbh->beginTransaction();

			$stmt->execute(array_values($data));

			$ids = $dbh->lastInsertId();

			$dbh->commit();

			return !empty($ids) ? true : false;
		} catch (PDOExecption $e) {

			$dbh->rollback();

			print "Error!: " . $e->getMessage() . "</br>";
		}
	} catch (PDOExecption $e) {

		print "Error!: " . $e->getMessage() . "</br>";
	}
}







function lastInsertId($table, $data = [])
{

	try {

		$colslist = '';

		$valueslist = '';

		foreach ($data as $key => $value) {

			$colslist .= !empty($colslist) ? ', ' . $key . ' ' : $key;

			$valueslist .= !empty($valueslist) ? ',?' : '?';
		}



		$dbh = db();

		$stmt = $dbh->prepare("INSERT INTO " . $table . " (" . $colslist . ") VALUES(" . $valueslist . ")");

		try {

			//$dbh->beginTransaction(); 

			$stmt->execute(array_values($data));

			//$dbh->commit(); 

			return $dbh->lastInsertId();
		} catch (PDOExecption $e) {

			$dbh->rollback();

			print "Error!: " . $e->getMessage() . "</br>";
		}
	} catch (PDOExecption $e) {

		print "Error!: " . $e->getMessage() . "</br>";
	}
}



function paginate($query, $pagelength = '10')
{

	$page = !empty($_REQUEST['page']) && $_REQUEST['page'] > 1 ? $_REQUEST['page'] : 1;

	$offset = $pagelength * ($page - 1);



	$totalQuery = db()->prepare($query);

	$totalQuery->execute();

	$total = $totalQuery->rowCount();



	$paginateQuery = db()->prepare($query . ' limit ' . $pagelength . ' offset ' . $offset);

	$paginateQuery->execute();

	$data = $paginateQuery->fetchAll(PDO::FETCH_ASSOC);



	return [

		"total" =>  $total,

		"per_page" =>  $pagelength,

		"current_page" =>  $page,

		"last_page" =>  ceil($total / $pagelength),

		"from" =>  $total > $offset ? $offset : $total,

		"to" => ($offset + $pagelength) < $total ? $offset + $pagelength : $total,

		"data" => $data

	];
}





function getFilePreview($url)
{

	$ext = explode('.', $url);

	$ext =  end($ext);

	$imgext = ['png', 'PNG', 'jpg', 'JPG', 'jpeg', 'JPEG', 'gif'];

	$fileext = ['docx', 'docs', 'pdf', 'zip', 'rar', 'tar', 'psd', 'ai'];

	if (in_array($ext, $imgext)) {

		return $url;
	} elseif (in_array($ext, $fileext)) {

		return asset('/images/ext/' . $ext . '.png');
	} else {

		return asset('/images/ext/unknown.png');
	}
}





function gridImage($url, $width = 75, $height = 75)
{

	$str = '';

	if (!empty($url)) {

		if (is_array($url)) {

			foreach ($url as $key => $value) {

				if (!empty($value)) {

					$str .= '<a  class="gridImage" href="' . upload_preview($value) . '" target="_blank" ><img  src="' . upload_preview($value) . '" width="' . $width . 'px" height="' . $height . 'px" ></a>';
				}
			}
		} else {

			$str = '<a class="gridImage" href="' . upload_preview($url) . '" target="_blank" ><img  src="' . upload_preview($url) . '" width="' . $width . 'px" height="' . $height . 'px" ></a>';
		}
	}

	return $str;
}



function gridlabel($data, $type = 'primary')
{

	$str = '';

	if (is_array($data)) {

		foreach ($data as $key => $value) {

			$str .= '<span class="badge  badge-' . $type . '" style="margin-right:5px">' . ucwords($value) . '</span>';
		}
	} else {

		$str .= '<span class="badge  badge-' . $type . '" style="margin-right:5px">' . ucwords($data) . '</span>';
	}

	return $str;
}



function gridswitch($id, $name, $value = '', $active = '1', $deactive = '0')
{

	$ck = ($value != '' && $active != '' && $value == $active) ? 'checked="checked"' : '';

	return '<label class="custom-switch action_update_column_switch"  ><input name="' . $name . '" type="checkbox" ' . $ck . ' data-id="' . $id . '" data-name="' . $name . '" data-active="' . $active . '" data-deactive="' . $deactive . '" ><span class="custom-slider round"></span></label>';
}







function getTableColumns($table)
{

	global $db;

	return array_column($db->pdoQuery("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name = '" . $table . "'")->results(), 'COLUMN_NAME');
}





function fillFormData($table, $formdata, $id, $type, $extra = [])
{

	$cols = getTableColumns($table);

	$final = [];

	foreach ($cols as $key => $value) {

		$final['%' . strtoupper($value) . '%'] = !empty($formdata[$value]) ? filtering($formdata[$value]) : '';
	}

	$final['%TYPE%'] = $type;

	$final['%ID%'] = filtering($id, 'input', 'int');

	return $final;
}



function formateCols($cols)
{

	$temp = [];

	foreach ($cols as $key => $value) {

		if (is_numeric($key)) {

			$temp[$value] = ucwords(str_replace(['-', '_'], [' ', ' '], $value));
		} else {

			$temp[$key] = $value;
		}
	}

	return $temp;
}



function formatId($type, $name)
{

	return $type . '_' . str_replace([' ', '[', ']', '-'], ['_', '_', '_', '_'], $name);
}



function formatTitle($name)
{

	$temp = explode('[', $name);

	if (count($temp) > 1) {

		$temp = explode(']', end($temp));

		$name = !empty($temp[0]) ? $temp[0] : $name;
	}

	return ucwords(str_replace([' ', '[', ']', '-', '_'], [' ', ' ', ' ', ' ', ' '], $name));
}





function formate_attr($type, $name, $value, $title, $attr)
{

	$rules = !empty($attr['rules']) ? $attr['rules'] : '';

	$msg = !empty($attr['msg']) ? $attr['msg'] : '';

	$generateRules = generateRules($rules, $msg);

	$id = !empty($attr['id']) ? $attr['id'] : formatId($type, $name);

	$option = '';

	if (!empty($attr['option']) && is_array($attr['option'])) {

		if ($type == 'radio') {

			foreach ($attr['option'] as $key => $optionval) {

				$option .= render_buffer("admin/field/radio_option.php", ['id' => $id . '_' . $key, 'name' => $name, 'value' => $key, 'title' => $optionval, 'checked' => ($key == $value) ? 'checked="checked"' : '', 'class' => !empty($attr['class']) ? $attr['class'] : '', 'rules' => $generateRules['rules'], 'msg' => $generateRules['msg']]);
			}
		} else if ($type == 'select') {

			foreach ($attr['option'] as $key => $optionval) {

				$option .= '<option value="' . $key . '" ' . (($value != $key) ? '' : ' selected="selected" ')  . ' > ' . $optionval . ' </option>';
			}
		} else if ($type == 'multiselect') {

			$value = !empty($value) ? $value : [];

			foreach ($attr['option'] as $key => $optionval) {

				$option .= '<option value="' . $key . '" ' . ((!in_array($key, $value)) ? '' : ' selected="selected" ')  . ' > ' . $optionval . ' </option>';
			}
		} else if ($type == 'checkbox') {

			foreach ($attr['option'] as $key => $optionval) {

				$value = !empty($value) ? $value : [];

				$option .= render_buffer("admin/field/checkbox_option.php", ['id' => $id . '_' . $key, 'name' => $name, 'value' => $key, 'title' => $optionval, 'checked' => (in_array($key, $value)) ? 'checked="checked"' : '', 'class' => !empty($attr['class']) ? $attr['class'] : '', 'rules' => $generateRules['rules'], 'msg' => $generateRules['msg']]);
			}
		}
	}





	return [

		'id' => $id,

		'name' => $name,

		'value' => $value,

		'title' => $title,

		'option' => $option,

		'class' => !empty($attr['class']) ? $attr['class'] : '',

		'titlewidth' => !empty($attr['titlewidth']) ? $attr['titlewidth'] : 2,

		'fieldwidth' => !empty($attr['fieldwidth']) ? $attr['fieldwidth'] : 8,

		'helper' => !empty($attr['helper']) ? $attr['helper'] : '',

		'fattr' => !empty($attr['fattr']) ? $attr['fattr'] : '',

		'pleaceholder' => !empty($attr['pleaceholder']) ? $attr['pleaceholder'] : 'Enter ' . $title,

		'rules' => $generateRules['rules'],

		'msg' => $generateRules['msg'],

		'isRequire' => $generateRules['isRequire'] ? '<font color="#FF0000">*</font>' : ''

	];
}







function prepareDataForFields($arr)
{

	return $arr;
}





function formFields($type, $name, $value, $attr = [])
{

	$title = !empty($attr['title'])  ? $attr['title'] : formatTitle($name);

	if ($type == 'html') {

		return empty($attr['view']) ? '' : render_buffer($attr['view'], ['data' => formate_attr($type, $name, $value, $title, $attr)]);
	}

	return render_buffer('admin/field/' . $type . '.php', ['data' => formate_attr($type, $name, $value, $title, $attr)]);
}



function form_text($name, $value, $attr = [])
{

	return formFields('text', $name, $value, $attr);
}



function generateRules($rulesData, $msgData = [])
{

	$rules = '';

	$msg = '';



	$rulesList = !empty($rulesData) ? explode('|', $rulesData) : [];

	$msgList = !empty($msgData) && is_array($msgData) ?  $msgData : [];

	$isRequire = false;

	foreach ($rulesList as $key => $value) {

		if (

			$value == 'required'

			|| $value == 'email'

			|| $value == 'url'

			|| $value == 'date'

			|| $value == 'number'

			|| $value == 'digits'

			|| $value == 'lettersonly'

			|| $value == 'ipv4'

			|| $value == 'ipv6'

			|| $value == 'integer'

			|| $value == 'nowhitespace'

		) {

			if ($value == 'required') {

				$isRequire = true;
			}

			$rules .= ' data-rule-' . $value . '="true" ';

			$msg .= !empty($msgList[$value]) ?  ' data-msg-' . $value . '="' . $msgList[$value] . '" ' : '';
		} elseif (

			substr($value, 0, 9) == 'minlength'

			|| substr($value, 0, 9) == 'maxlength'

			|| substr($value, 0, 11) == 'rangelength'

			|| substr($value, 0, 3) == 'min'

			|| substr($value, 0, 3) == 'max'

			|| substr($value, 0, 5) == 'range'

			|| substr($value, 0, 7) == 'equalto'

			|| substr($value, 0, 6) == 'remote'

			|| substr($value, 0, 6) == 'accept'

			|| substr($value, 0, 7) == 'pattern'

		) {

			$temp = explode(':', $value);

			$rules .= ' data-rule-' . $temp[0] . '="' . $temp[1] . '" ';

			$msg .=  !empty($msgList[$temp[0]]) ?  ' data-msg-' . $temp[0] . '="' . $msgList[$temp[0]] . '" ' : '';
		}
	}

	return ['rules' => $rules, 'msg' => $msg, 'isRequire' => $isRequire];
}



function php_validation($rules, $data)
{



	$rules = ['name' => ['required' => 'Name is required']];



	foreach ($rules as $key => $value) { }
}





function form_radio($name, $value, $attr = [])
{

	return formFields('radio', $name, $value, $attr);
}



function form_textarea($name, $value, $attr = [])
{

	return formFields('textarea', $name, $value, $attr);
}



function form_select($name, $value, $attr = [])
{

	return formFields('select', $name, $value, $attr);
}



function form_checkbox($name, $value, $attr = [])
{

	return formFields('checkbox', $name, $value, $attr);
}



function form_display($name, $value, $attr = [])
{

	return formFields('display', $name, $value, $attr);
}



function form_date($name, $value, $attr = [])
{

	return formFields('date', $name, $value, $attr);
}



function form_datetime($name, $value, $attr = [])
{

	return formFields('datetime', $name, $value, $attr);
}



function form_time($name, $value, $attr = [])
{

	return formFields('time', $name, $value, $attr);
}



function form_ckeditor($name, $value, $attr = [])
{

	return formFields('ckeditor', $name, $value, $attr);
}







function getProducts()
{

	$data = select("SELECT * FROM `pages` WHERE page_type != 'cms'");

	foreach ($data as $key => $value) {

		$data[$key]['page_json'] = json_decode($value['page_json'], true);

		$data[$key]['seo_meta'] = json_decode($value['seo_meta'], true);
	}

	return !empty($data) ? $data : [];
}



function export_to_csv($data, $filename, $delimiter = ',')
{

	if (!empty($data)) {

		$usertype_array = array_keys($data[0]);
	} else {

		$usertype_array = ['No data Found'];
	}



	$final_result = array($usertype_array);

	foreach ($data as $k => $v) {

		$final_result = array_merge($final_result, array(array_values($v)));
	}



	$temp_memory = fopen('php://memory', 'w');

	foreach ($final_result as $line) {

		fputcsv($temp_memory, $line, $delimiter);
	}

	fseek($temp_memory, 0);

	header('Content-Type: application/csv');

	header('Content-Disposition: attachement; filename="' . $filename . '_' . date('YmdHis') . '.csv";');

	fpassthru($temp_memory);

	exit;
}





function export_to_excel($data, $filename)
{



	header("Content-Type: application/xls");

	header("Content-Disposition: attachment; filename=" . $filename . '_' . date('YmdHis') . ".xls");

	header("Pragma: no-cache");

	header("Expires: 0");



	$sep = "\t";

	if (!empty($data)) {

		foreach ($data[0] as $key => $value) {

			echo $key . "\t";
		}
	} else {

		echo 'No record found' . "\t";
	}



	print("\n");

	if (!empty($data)) {

		foreach ($data as $key => $value) {

			$schema_insert = "";

			foreach ($value as $key2 => $value2) {

				if (!isset($value2))

					$schema_insert .= "NULL" . $sep;

				elseif ($value2 != "")

					$schema_insert .= "$value2" . $sep;

				else

					$schema_insert .= "" . $sep;
			}

			$schema_insert = str_replace($sep . "$", "", $schema_insert);

			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);

			$schema_insert .= "\t";

			print(trim($schema_insert));

			print "\n";
		}
	}
}



function dd($data)
{

	if (is_array($data)) {

		print_r($data);
	} else {

		echo $data;
	}

	die;
}


function trim_arr($data)
{
	if (!empty($data)) {
		foreach ($data as $key => $value) {
			$data[$key] = trim($value);
		}
	}
	return $data;
}

function format_data($data, $type = 1)
{
	if (empty($data)) {
		return [];
	} else {
		if ($type == 1) {
			return explode('~', $data);
		}
	}
	return [];
}

function getFilename($path)
{
	$temp = explode('/', $path);
	return end($temp);
}

function fill_image($path, $name)
{
	$str = '';
	$str .= '<div class="row custom-wrapper">';
	$str .= '<div class="col-md-8">';
	$str .= '<input class="form-control custom-file one_file" name="' . $name . '" id="image_' . $name . '" type="file">';
	$str .= '</div>';
	$str .= '<div class="col-md-4">';
	$str .= '<div class="file_preview_wrapper row single">';
	if ($path != '') {
		$str .= '<div class="col-md-12 file_preview_item" data-img="' . $path . '" >';
		$str .= '<input type="hidden"  name="' . $name . '" value="' . $path . '" >';
		$str .= '<a href="#" class="del_file"> <i class="fa fa-trash" aria-hidden="true"></i> </a>';
		$str .= '<a href="' . $path . '" target="_blank" ><img src="' . $path . '"></a>';
		$str .= '<p>' . getFilename($path) . '</p>';
		$str .= '</div>';
	}
	$str .= '</div>';
	$str .= '</div>';
	$str .= '</div>';
	return $str;
}


function get_bonuspoints(){
	$q =  "SELECT bp.*, cls1.class_name, concat(st1.fname,' ',st1.lname) as student_name, 
	(select sum(sc.points) from tbl_class as cls 
	LEFT JOIN tbl_chapter as chp on cls.class_id = chp.class_id
	LEFT JOIN tbl_lesson as ls on chp.chapter_id = ls.chapter_id
	left JOIN admins as tech on chp.teacher_id = tech.id
	LEFT JOIN tbl_lesson_module as lm on ls.lesson_id = lm.lesson_id and lm.status = 2 
	LEFT JOIN tbl_student_score as sc on lm.lesson_module_id = sc.lesson_module_id
	WHERE cls.class_id = bp.class_id and sc.student_id = bp.student_id
	GROUP BY cls.class_id) achived_points
	FROM `tbl_bonus_points` as bp
	LEFT JOIN tbl_class as cls1 on bp.class_id = cls1.class_id
	LEFT JOIN admins as st1 on bp.student_id = st1.id";
	$apoints = selectOne($q.' where bp.student_id = '.$_SESSION['admin']['id'].'  ');
	$napoints = $apoints['achived_points'] ?? 0;
	$nbpoints = $apoints['bonus_points'] ?? 0; 
	return $napoints + $nbpoints;
}