<?php
/**
 * Simple NUTS CRUD for Front Office plugin
 *
 * @package Nuts-Component
 * @version 1.0
 */
class NutsCRUD
{
	private $page_language = 'en';

	private $allowed_actions = array();
	private $db_table = "";
	private $sql_where_added = "";
	public $page_url = "";

	/**
	 * controller primary variable
	 * @param string $plugin
	 */
	public function __construct($plugin, $db_table, $sql_where_added='')
	{
		global $nuts;

		$_GET['ID'] = (int)@$_GET['ID'];
		$this->page_language = $nuts->language;
		$this->page_url = "/".$nuts->language.'/'.$nuts->vars['ID'].'.html';
		$this->db_table = $db_table;
		$this->sql_where_added = $sql_where_added;
		
		// get allowed action for this group
		$sql = "SELECT
						Name
				FROM
						NutsMenuRight
				WHERE
						NutsGroupID = {$_SESSION['NutsGroupID']} AND
						NutsMenuID IN (SELECT ID FROM NutsMenu WHERE Name = '$plugin' AND Deleted = 'NO')";
		$nuts->doQuery($sql);
		while($row = $nuts->dbFetch())
		{
			$this->allowed_actions[] = $row['Name'];
		}

		if(!isset($_GET['do']))
		{
			if(in_array('list', $this->allowed_actions))
				$_GET['do'] = 'list';
			else
				$_GET['do'] = $this->allowed_actions[0];
		}

		// verify right allowed
		if(!in_array($_GET['do'], $this->allowed_actions))
		{
			// die("Error: action not allowed");
			$const = constant('PRIVATE_PAGE_FORBIDDEN_URL_'.$this->page_language);
			$nuts->redirect($const);
		}

		if($_GET['do'] == 'add')$_GET['ID'] = 0;

		// @todo: verify record owner for view, edit and delete action
		if(in_array($_GET['do'], array('view','edit', 'delete')))
		{
			$deleted_sql = ($_GET['do'] == 'delete') ? "  " : " AND Deleted = 'NO' "; # prevent already Deleted flag


			$sql = "SELECT ID FROM {$this->db_table} WHERE ID = {$_GET['ID']} $deleted_sql ";
			if(!empty($sql_where_added))
				$sql_where_added .= " AND ".$sql_where_added;
			
			$nuts->doQuery($sql);
			if($nuts->dbNumRows() == 0)
			{
				$const = constant('PRIVATE_PAGE_FORBIDDEN_URL_'.strtoupper($this->page_language));
				$nuts->redirect($const);
			}
		}

		// init form
		if(in_array($_GET['do'], array('add', 'edit')))
		{
			if($this->page_language == 'fr')
				$nuts->formSetLang('fr');
			else
				$nuts->formSetLang('en');
		}
	}

	private $listCols = array();
	/**
	 * List add column
	 *
	 * @param string $fieldName sql field in query
	 * @param string $label if empty label is fieldname
	 * @param boolean $orderBy add order by in column (default false)
	 * @param string $style
	 */
	public function listAddCol($fieldName, $label='', $orderBy=false, $style='')
	{
		if(empty($label))$label = $fieldName;
		$this->listCols[] = array('fieldName' => $fieldName, 'label' => $label, 'order_by' => $orderBy, 'style' => $style);
	}

	private $listSelectExeption = array();
	
	/**
	 * remove field to select
	 * @param string $field
	 */
	public function listAddSelectFieldException($field)
	{
		$this->listSelectExeption[] = $field;
	}


	private $listSelectFieldsAdded = array();
	/**
	 * Add field to select
	 * @param string $field
	 */
	public function listAddSelectField($field)
	{
		$this->listSelectFieldsAdded[] = $field;
	}

	/**
	 * Render list
	 * @param int $nb_page (default 20)
	 * @param string $hookData function to hookData
	 */
	public function listRender($nb_page = 20, $hookData='')
	{
		/**
		 * @var $nuts Page
		 */
		global $nuts;
	
		$sql = "SELECT\n";
		
		foreach($this->listCols as $col)
		{
			if(!in_array($col['fieldName'], $this->listSelectExeption))
				$sql .= "		{$col['fieldName']},";
		}

		if(count($this->listSelectFieldsAdded) > 0)
		{
			foreach($this->listSelectFieldsAdded as $col)
				$sql .= "		{$col},";
		}

		$sql = substr($sql, 0, -1);
		$sql .= "\nFROM\n\t\t".$this->db_table;
		$sql .= "\nWHERE\n\t\t"."Deleted = 'NO'\n";

		if(!empty($this->sql_where_added))
			$sql .= " AND ".$this->sql_where_added;
		trim($sql);

		// dynamic add button
		$template_add = '';
		$label_add = ($this->page_language == 'fr') ? 'Ajouter' : 'Add';
		if(in_array('add', $this->allowed_actions))
		{
			$template_add = <<<EOF
			<div class="nuts_crud_add">
				<a href="{$this->page_url}?do=add">$label_add</a>
			</div>
EOF;
		}

		// dynamic table
		$template_table = '<table class="nuts_crud">'."\n";

		// add th header
		$template_table .= '<tr>';
		$order_bys = array();
		foreach($this->listCols as $col)
		{
			$order_by = ($col['order_by'] == true) ? '{_order_by::'.$col['fieldName'].'}' : '';
			if($col['order_by'] == true)$order_bys[] = $col['fieldName'];
			
			$template_table .= "	<th style=\"{$col['style']}\">{$col['label']} $order_by</th>\n";
		}
		$nuts->ShowRecordsOrderBy($order_bys);

		
		// add dynamic th edit button
		if(in_array('edit', $this->allowed_actions))
			$template_table .= "	<th style=\"width:20px;text-align:center;\">&nbsp;</th>\n";
		
		// add dynamic th delete button
		if(in_array('edit', $this->allowed_actions))
			$template_table .= "	<th style=\"width:20px;text-align:center;\">&nbsp;</th>\n";

		// add dynamic th delete button
		if(in_array('view', $this->allowed_actions))
			$template_table .= "	<th style=\"width:20px;text-align:center;\">&nbsp;</th>\n";

		$template_table .= "</tr>\n";
		

		// add dynamic tr button
		$template_table .= "<bloc::loop>\n";
		$template_table .= "<tr>\n";
		foreach($this->listCols as $col)
		{
			$template_table .= "	<td style=\"{$col['style']}\">{{$col['fieldName']}}</td>\n";
		}

		// add dynamic td view button
		if(in_array('view', $this->allowed_actions))
		{
			$label_view = ($this->page_language == 'fr') ? 'Voir cet enregistrement' : 'View this record';
			$template_table .= "	<td class=\"nuts_crud_list_view\"><a title=\"$label_view\" href=\"{$this->page_url}?do=view&ID={ID}\">&nbsp;</a></td>\n";
		}
		// add dynamic td edit button
		if(in_array('edit', $this->allowed_actions))
		{
			$label_edit = ($this->page_language == 'fr') ? 'Editer cet enregistrement' : 'Edit this record';
			$template_table .= "	<td class=\"nuts_crud_list_edit\"><a title=\"$label_edit\" href=\"{$this->page_url}?do=edit&ID={ID}\">&nbsp;</a></td>\n";
		}
		// add dynamic delete button
		if(in_array('edit', $this->allowed_actions))
		{
			$label_delete = ($this->page_language == 'fr') ? 'Supprimer cet enregistrement' : 'Delete this record';
			$confirm_message = ($this->page_language == 'fr') ? 'Voulez-voulez supprimer cet enregistrement #{ID}' : 'Would you like to delete this record #{ID}';
			$template_table .= "	<td class=\"nuts_crud_list_delete\"><a title=\"$label_delete\" href=\"javascript:;\" onclick=\"if(c=confirm('$confirm_message'))document.location.href='{$this->page_url}?do=delete&ID={ID}';\">&nbsp;</a></td>\n";
		}
		
		
		$template_table .= "</tr>\n";
		$template_table .= "</bloc::loop>\n";

		
		$template_table .= '</table>';

		// pager
		$template_pager = <<<EOF
		<div class="nuts_crud_pager" id="pager">

			<bloc::start>
				<a class="pager_start" href="{_Url}">&lt;&lt; Début</a>
			</bloc::start>
			<bloc::previous>
				<a class="pager_previous" href="{_Url}">&lt; Précédent</a>
			</bloc::previous>

			<bloc::pager>
				<bloc::out><a href="{_Url}">{_Page}</a></bloc::out>
				<bloc::in><a class="selected">{_Page}</span></bloc::in>
			</bloc::pager>

			<bloc::next>
				<a class="pager_next" href="{_Url}">Suivant &gt;</a>
			</bloc::next>
			<bloc::end>
				<a class="pager_end"  href="{_Url}">Fin &gt;&gt;</a>
			</bloc::end>
			
		</div>
		<script>
			var pager_page = "{_PageNumber}";
			var pager_count = "{_PageCount}";
		</script>
EOF;
		// norecord
		$label_norecord = ($this->page_language == 'fr') ? 'Aucun enregistrement trouvé' : 'No record found';
		$template_norecord = <<<EOF

		<!-- norecord -->
		<bloc::norecord>
		<div class="norecord">
			$label_norecord
		</div>
		</bloc::norecord>
		<!-- /norecord -->
		
EOF;

		// template
		$template = <<<EOF

		$template_add

		<bloc::data>
			$template_table

			$template_pager

			$template_norecord
		</bloc::data>
EOF;
		$template = trim($template);

		$nuts->createVirtualTemplate($template);
		$nuts->showRecords($sql, $nb_page, $hookData);
		$out = $nuts->output();
		
		$nuts->setNutsContent($out);

	}

	private $formCols = array();
	
	/**
	 * Add form field
	 *
	 * @param string $name
	 * @param string $label if empty label = name
	 * @param string $type (text, date, email, textarea, nuts_login, nuts_password, select-sql, select, image, file)
	 * @param boolean $required
	 * @param string $attributes field attributes
	 * @param array $options option (field for select-sql)
	 */
	public function formAddField($name, $label, $type, $required, $attributes='', $option=array())
	{
		global $nuts;

		if(empty($label))$label = $name;

		// image
		if($type == 'image')
		{
			$option['url'] = str_replace(WEBSITE_PATH, WEBSITE_URL, $option['path']);

			// parent
			if(!isset($option['constraint']))$option['constraint'] = false;
			if(!isset($option['width']))$option['width'] = '';
			if(!isset($option['height']))$option['height'] = '';
			if(!isset($option['exts']))$option['exts'] = 'jpg,gif,png';
			if(!isset($option['size']))$option['size'] = '2Mo';
			if(!isset($option['suffix']))$option['suffix'] = '';
			if(!isset($option['background_color']))$option['background_color'] = array(255, 255, 255);

			// thumbnail
			if(!isset($option['thumbnail_new']))$option['thumbnail_new'] = false;
			if(!isset($option['thumbnail_width']))$option['thumbnail_width'] = '';
			if(!isset($option['thumbnail_height']))$option['thumbnail_height'] = '';
			if(!isset($option['thumbnail_constraint']))$option['thumbnail_constraint'] = false;
			if(!isset($option['thumbnail_background_color']))$option['thumbnail_background_color'] = array(255, 255, 255);
			
		}

		// dynamic form name
		$nuts->formSetObjectName($name, '<i18n>'.$label.'<i18n>');
		$this->formCols[] = array('name' => $name, 'label' => $label, 'type' => $type, 'required' => $required, 'attributes' => $attributes, 'option' => $option);
	}
	
	private $formSqlAdded = array();

	/**
	 * Add sql in insert/update where clause
	 *
	 * @param string $fieldName
	 * @param string $value
	 */
	public function formAddSql($fieldName, $value)
	{
		$this->formSqlAdded[$fieldName] = $value;
	}

	/**
	 * Add control for field unicity
	 * @param string $fieldName
	 */
	public function formSetUniq($fieldName)
	{
		global $nuts;
		if($_POST && !empty($_POST[$fieldName]))
		{
			$nuts->dbSelect("SELECT ID FROM {$this->db_table} WHERE $fieldName = '%s' AND ID != {$_GET['ID']} LIMIT 1", array($_POST[$fieldName]));
			if($nuts->dbNumrows() != 0)
			{
				$msg = ($this->page_language == 'fr') ? "Le champ `$fieldName` existe déjà" : "Field `$fieldName` already exists";
				$nuts->addError($fieldName, $msg);
			}
		}
	}





	/**
	 * Allow to atomatically generate LogActionCreate, LogActionUpdate, LogActionDelete sql
	 * @var boolean
	 */
	public $sqlActionAuto = true;

	/**
	 * Render form
	 * @return false if not submit or ID of record
	 */
	public function formRender()
	{
		/**
		 * @var $nuts Page
		 */
		global $nuts;
		
		// special action
		if($this->sqlActionAuto)
		{
			$action = ($_GET['do'] == 'add') ? 'LogActionCreate' : 'LogActionUpdate';
			$this->formAddSql($action.'NutsUserID', $_SESSION['NutsUserID']);
			$this->formAddSql($action.'NutsGroupID', $_SESSION['NutsGroupID']);
			
			// update systeme date
			$this->formAddSql($action.'DateGMT', nutsGetGMTDate());
			$this->formAddSql($action.'Date', date('Y-m-d H:i:s'));
			
		}
		
		$nuts->formSetName('nuts_crud_form');
		$nuts->formSetDisplayMode('T');


		if($_GET['do'] == 'add')
		{
			$r = array();
			foreach($this->formCols as $field)
			{
				// generate password
				if($field['type'] == 'nuts_password')
				{
					$chars = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
					$i = 0;
					$password = "";
					while ($i <= 8)
					{
						$password .= $chars[mt_rand(0,strlen($chars)-1)];
						$i++;
					}
					
					$r[$field['name']] = $password;
				}
			}
			
			if(count($r) > 0)
				$nuts->formInit($r);
		}

		if($_GET['do'] == 'edit')
		{
			$sql = "SELECT * FROM {$this->db_table} WHERE ID = {$_GET['ID']}";
			$nuts->doQuery($sql);
			$r = $nuts->dbFetch();

			foreach($this->formCols as $field)
			{
				if($field['type'] == 'date')
				{
					$r[$field['name']] = $nuts->db2date($r[$field['name']]);
				}

				if($field['type'] == 'nuts_password')
				{
					$r[$field['name']] = nutsCrypt($r[$field['name']], false);
				}

			}

			$nuts->formInit($r);
		}
		
		$fields = "";
		foreach($this->formCols as $field)
		{
			$label = '<i18n>'.$field['label'].'</i18n>';

			$required = '';
			if($field['required'])
				$required = '<span class="required">*</span>';

			$input = '';
			
			// type:text or date or email
			if(in_array($field['type'], array('text','date','email','nuts_login', 'nuts_password')))
			{
				$input = '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" '.$field['attributes'].' />';
			}
			// textarea
			elseif($field['type'] == 'textarea')
			{
				$input = '<textarea name="'.$field['name'].'" id="'.$field['name'].'" '.$field['attributes'].'></textarea>';
			}
			// select-sql
			elseif($field['type'] == 'select-sql')
			{
				$field_option = $field['option']['field'];
				$table = str_replace('ID', '', $field['name']);
				$sql = "SELECT ID, $field_option FROM $table WHERE Deleted = 'NO' ORDER BY $field_option";
				$nuts->doQuery($sql);

				$input = '<select id="'.$field['name'].'" name="'.$field['name'].'" '.$field['attributes'].'>'."\n";
				$input .= '<option value=""></option>'."\n";
				while($row = $nuts->dbFetch())
				{
					$input .= '<option value="'.$row['ID'].'">'.$row[$field_option].'</option>'."\n";
				}
				
				$input .= '</select>';
			}
			// select
			elseif($field['type'] == 'select')
			{
				$tmp_options = "";
				$options = $field['option']['options'];
				foreach($options as $option)
				{
					if(is_array($option))
						$tmp_options .= '<option value="'.$option[1].'">'.$option[0].'</option>'."\n";
					else
					{
						$option_label = ucfirst(strtolower($option));
						$tmp_options .= '<option value="'.$option.'">'.$option_label.'</option>'."\n";
					}
				}

				$input = '<select id="'.$field['name'].'" name="'.$field['name'].'" '.$field['attributes'].'>'."\n";
				$input .= $tmp_options;
				$input .= '</select>';
			}
			// file, image
			elseif($field['type'] == 'file' || $field['type'] == 'image')
			{
				$input = '<input type="file" name="'.$field['name'].'" id="'.$field['name'].'" '.$field['attributes'].' /> ';
				$label_cancel = ($this->page_language == 'fr') ? 'Annuler' : 'Cancel';
				$input .= '<a href="javascript:;" onclick="$(\'#'.$field['name'].'\').val(\'\');">'.$label_cancel.'</a>';

				$preview = '';
				if($field['type'] == 'image' && $_GET['do'] == 'edit')
				{
					// mode edit
					$sql = "SELECT {$field['name']}Image FROM {$this->db_table} WHERE ID = {$_GET['ID']}";
					$nuts->doQuery($sql);
					if($nuts->dbNumRows() == 1)
					{
						$img = $nuts->dbGetOne();
						if(!empty($img))
						{
							$image_url = $field['option']['url'].'/'.$img;
							$preview = '<a target="_blank" href="'.$image_url.'" class="nuts_crud_preview"><img src="'.$image_url.'" /></a>';
							$input = $preview.' '.$input;
						}
					}
				}
			}
			
			$fields .= "\n<div id=\"wrapper_{$field['name']}\" class=\"wrapper\">\n";
			$fields .= "\n<span class=\"label\">{$field['label']} $required  :</span>\n";
			$fields .= "\n{$input}\n";
			$fields .= "\n</div>\n";
		}

		// required
		$req_label = ($this->page_language == 'fr') ? "Champs obligatoires" : "<i18n>Required fields</i18n>";
		$fields .= "\n<div class=\"wrapper form_required\">\n";
		$fields .= "\n<span class=\"required\">*</span> $req_label\n";
		$fields .= "\n</div>\n";

		// add final bottom
		$fields .= "\n<div class=\"wrapper form_bottom\">\n";
		$fields .= '<input type="reset" class="reset" onclick="document.location.href=\''.$this->page_url.'\'" />';
		$fields .= '<input type="submit" class="submit" />';
		$fields .= "\n</div>\n";

		// dynamic validation
		foreach($this->formCols as $field)
		{
			if($field['required'])
			{
				if($field['type'] == 'checkbox' || $field['type'] == 'select-multiple')
					$nuts->notEmpty($field['name'].'[]');
				else
					$nuts->notEmpty($field['name']);
			}

			if($field['type'] == 'date')
				$nuts->date($field['name'], "d/m/Y");

			if($field['type'] == 'email')
				$nuts->email($field['name']);

			if($field['type'] == 'nuts_login')
			{
				$nuts->alphaNumeric($field['name'], ".-_");
				$this->formSetUniq($field['name']);
			}

			if($field['type'] == 'nuts_password')
				$nuts->alphaNumeric($field['name'], ".-_");
			
			if($field['type'] == 'image')
			{
				if(isset($_FILES[$field['name']]) && !$_FILES[$field['name']]['error'])
					$nuts->fileControl($field['name'], false, $field['option']['size'], '', $field['option']['exts']);
			}

			// if(!empty($field['OtherValidation']))eval($field['OtherValidation']);
		}

		// caption
		if($_GET['do'] == 'add')
			$label_caption = ($this->page_language == 'fr') ? 'Ajouter un enregistrement' : 'Add new record';
		else
			$label_caption = ($this->page_language == 'fr') ? 'Editer un enregistrement' : 'Edit record';
		$caption = "<div class=\"caption\">{$label_caption}</div>\n";

		// form valid code
		if($_GET['do'] == 'add')
			$form_valid_code = ($this->page_language == 'fr') ? 'Votre enregistrement a bien été créé' : 'Your record has been created';
		else
			$form_valid_code = ($this->page_language == 'fr') ? 'Votre enregistrement a bien été modifié' : 'Your record has been updated';


		$label_return = ($this->page_language == 'fr') ? 'Retour à la page précédente' : 'Return to previous page';

		$template = <<<EOF
<form class="nuts_form" name="nuts_crud_form" id="nuts_crud_form" method="post" enctype="multipart/form-data">

	<ul id="form_error">
		<bloc::form_error>
		<li>{msg}</li>
		</bloc::form_error>
	</ul>

	<div id="layout_form_nuts_crud" class="layout_form">

		<div id="wrapper_form_nuts_crud" class="wrapper_form">
			$caption

			$fields
		</div>

	</div>
</form>

<script type="text/javascript">
if($('ul#form_error li').length == 0)
	$('ul#form_error').remove();
</script>

<bloc::form_valid>
<div id="form_valid">
	{$form_valid_code}
	<br/>
	<br/>
	<a href="{$this->page_url}">$label_return</a>

</div>
</bloc::form_valid>
EOF;

		$nuts->createVirtualTemplate($template);

		$CUR_ID = 0;
		if($nuts->formIsValid())
		{
			$nuts->sanitizePost();

			// hack by type
			foreach($this->formCols as $field)
			{
				if($field['type'] == 'date')
				{
					$_POST[$field['name']] = $nuts->date2db($_POST[$field['name']]);
				}
				
				if($field['type'] == 'nuts_password')
				{
					$_POST[$field['name']] = nutsCrypt($_POST[$field['name']], true);
				}
			}

			// save record
			$rec = array();
			foreach($this->formCols as $field)
			{
				if(!in_array($field['type'], array('file', 'image')))
					$rec[$field['name']] = $_POST[$field['name']];
			}
			
			foreach($this->formSqlAdded as $key => $val)
				$rec[$key] = $val;

			$ff = array();
			if($_GET['do'] == 'add')
			{
				$CUR_ID = $nuts->dbInsert($this->db_table, $rec, $ff, true);
			}
			else
			{
				$nuts->dbUpdate($this->db_table, $rec, "ID={$_GET['ID']}", $ff);
				$CUR_ID = $_GET['ID'];
			}

			// update for file and image
			foreach($this->formCols as $field)
			{
				if(in_array($field['type'], array('file', 'image')) && isset($_FILES[$field['name']]) && !$_FILES[$field['name']]['error'])
				{
					$img_name = (empty($field['option']['suffix'])) ? $CUR_ID : $CUR_ID.$field['option']['suffix'];
					$nuts->fileUpload($field['name'], $field['option']['path'], $img_name);
					$nuts->imgThumbnail($field['option']['width'], $field['option']['height'], $field['option']['constraint'], $field['option']['background_color']);

					// update field
					$extension = end(explode('.', $_FILES[$field['name']]['name']));
					$extension = strtolower($extension);
					$img_name_full = $img_name.'.'.$extension;
					$nuts->dbUpdate($this->db_table, array("{$field['name']}Image" => $img_name_full), "ID=$CUR_ID");
				}
			}
		}

		
		$out = $nuts->output();
		$nuts->setNutsContent($out);
		
		return $CUR_ID;
	}


	private $deleteSqlAdded = array();

	/**
	 * Add sql in delete records
	 *
	 * @param string $fieldName
	 * @param string $value
	 */
	public function deleteAddSql($fieldName, $value)
	{
		$this->deleteSqlAdded[$fieldName] = $value;
	}

	/**
	 * Delete records
	 */
	public function delete()
	{
		global $nuts;

		// add special action
		if($this->sqlActionAuto)
		{
			$this->deleteAddSql('LogActionDeleteNutsUserID', $_SESSION['NutsUserID']);
			$this->deleteAddSql('LogActionDeleteNutsGroupID', $_SESSION['NutsGroupID']);
			$this->deleteAddSql('LogActionDeleteDateGMT', nutsGetGMTDate());
		}


		$sql = "UPDATE
						{$this->db_table}
				SET
						Deleted = 'YES' ";
		if(count($this->deleteSqlAdded) > 0)
		{
			foreach($this->deleteSqlAdded as $key => $val)
				$sql .= ','.$key."= '".addslashes($val)."'\n";
		}
		
		$sql .= "WHERE
						ID = {$_GET['ID']}";

		$nuts->doQuery($sql);
		$nuts->redirect($this->page_url);

	}

	private $viewH1 = "";

	/**
	 * Assign h1 title
	 * @param string $h1
	 */
	public function viewSetH1($h1)
	{
		$this->viewH1 = $h1;
	}

	private $viewContent = "";

	/**
	 * Assign view content
	 * @param string $content
	 */
	public function viewSetContent($content)
	{
		$this->viewContent = $content;
	}

	/**
	 * Render view
	 */
	public function viewRender()
	{
		global $nuts;
		
		$content = "";
		if(!empty($this->viewH1))
			$nuts->vars["H1"] = $this->viewH1;

		$nuts->setNutsContent($this->viewContent);

	}

	/**
	 * Get data in a row
	 * @return array record
	 */
	public function viewGetRow()
	{
		global $nuts;
		$sql = "SELECT * FROM {$this->db_table} WHERE ID = {$_GET['ID']}";

		$nuts->doQuery($sql);
		$row = $nuts->dbFetch();
		
		return $row;
	}


}


