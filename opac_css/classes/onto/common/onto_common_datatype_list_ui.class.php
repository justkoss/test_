<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_list_ui.class.php,v 1.17.2.1 2019-11-25 12:52:58 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_list_ui
 * 
 */
class onto_common_datatype_list_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propri�t� concern�e
	 * @param restriction $restrictions le tableau des restrictions associ�es � la propri�t� 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl, $lang;
		
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset), $form);	
		
		$options_values = array();
		if (isset($property->pmb_list_item)) {
			foreach ($property->pmb_list_item as $list_item) {
				$options_values[$list_item['id']] = $list_item['value'];
			}
		}
		if (isset($property->pmb_list_query)) {
		    //on rajoute la langue si besoin dans le requete
		    $query = str_replace('$lang', $lang, $property->pmb_list_query);
		    $result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
    			while ($row = pmb_mysql_fetch_array($result)) {
    				$options_values[$row[0]] = $row[1];
    			}
			}
		}
		
		$content='';	
		$form=str_replace("!!onto_new_order!!",0 , $form);
	
		$row=$ontology_tpl['form_row_content'];
		
		$cp_options = array();
		if (!empty($property->cp_options)) {
			$cp_options = encoding_normalize::json_decode($property->cp_options, true);
		}
		if (!empty($cp_options) && !empty($cp_options["CHECKBOX"]) && $cp_options["CHECKBOX"][0]["value"] == "yes") {
			$inside_row = static::get_checkbox_form($item_uri, $property, $restrictions, $datas, $options_values);
		} else {
			$inside_row = static::get_selector_form($item_uri, $property, $restrictions, $datas, $options_values);
		}

		$row=str_replace("!!onto_inside_row!!", $inside_row , $row);

		$input='';

		$row=str_replace("!!onto_row_inputs!!",$input , $row);
		$row=str_replace("!!onto_row_order!!",0 , $row);

		$content.=$row;
				
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form =  self::get_form_with_special_properties($property, $datas, $instance_name, $form);		
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
		
	} // end of member function get_form

	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs � afficher associ�es � la propri�t�
	 * @param property property la propri�t� � utiliser
	 * @param string instance_name nom de l'instance
	 * 
	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
		
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->get_label().' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
		
	} // end of member function get_display
	
	public static function get_hidden_fields($property,$datas, $instance_name, $flag = false) {
		global $msg,$charset,$ontology_tpl;
		
		$form=$ontology_tpl['form_row_hidden'];
		
		$content='';
		
		if(sizeof($datas)){
			$form=str_replace("!!onto_new_order!!", '0', $form);
						
			$formated_values = array();
			foreach($datas as $key=>$data){
				$formated_value = $data->get_formated_value();
				if (is_array($formated_value)) {
					$formated_values = array_merge($formated_values, $formated_value);
				} else {
					$formated_values[] = $formated_value;
				}
			}
			$row = $ontology_tpl['form_row_content_list_hidden'];
			
			$form_row_content_list_item_hidden = '';
			if (is_array($formated_values)) {
				$row = str_replace("!!form_row_content_list_hidden_values!!", implode(',', $formated_values), $row);
				foreach ($formated_values as $formated_value) {
					$form_row_content_list_item_hidden.= str_replace("!!onto_row_content_hidden_value!!",htmlentities($formated_value, ENT_QUOTES, $charset), $ontology_tpl['form_row_content_list_item_hidden']);
				}
			} else {
				$row = str_replace("!!form_row_content_list_hidden_values!!", $formated_values, $row);
				$form_row_content_list_item_hidden = str_replace("!!onto_row_content_hidden_value!!",htmlentities($formated_values, ENT_QUOTES, $charset) ,$ontology_tpl['form_row_content_list_item_hidden']);
			}
			$row = str_replace("!!form_row_content_list_item_hidden!!", $form_row_content_list_item_hidden, $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row = str_replace("!!onto_row_order!!", '0', $row);
	
			$content.= $row;
		} else {	
				
			$form=str_replace("!!onto_new_order!!","0" , $form);
					
			$row = $ontology_tpl['form_row_content_hidden'];
			$row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		if ($flag) {
			$form=$content;
		} else {
			$form=str_replace("!!onto_rows!!",$content ,$form);
		}
				
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
	}
	
	public static function get_selector_form($item_uri, $property, $restrictions, $datas, $options_values) {
		global $ontology_tpl, $charset;
		
		$inside_row = ($restrictions->get_max() != 1 ? $ontology_tpl['form_row_content_list_multi'] : $ontology_tpl['form_row_content_list']);
		$inside_row.= $ontology_tpl['form_row_content_type'];
		
		$list_values_to_display = static::get_list_values_to_display($property);
		
		$options = '';
		$values = array();
		if (empty($datas)) {
			$datas = array();
		}
		foreach($datas as $key=>$data){
			$formated_value = $data->get_formated_value();
			if (is_array($formated_value)) {
				$values = array_merge($values, $formated_value);
			} else {
				$values[] = $formated_value;
			}
		}
		foreach($options_values as $id => $value){
			if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
				continue;
			}
			$options.= '<option value="'.htmlentities($id, ENT_QUOTES, $charset).'" '.(in_array($id, $values) ? 'selected="selected"' : '').'>'.htmlentities($value, ENT_QUOTES, $charset).'</option>';
		}
		/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
		$inside_row = str_replace("!!onto_row_content_list_options!!", $options, $inside_row);
		$inside_row = str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
		
		return $inside_row;
	}
	
	public static function get_checkbox_form($item_uri, $property, $restrictions, $datas, $options_values) {
		global $ontology_tpl, $charset;
		
		$radio_or_checkbox = ($restrictions->get_max() != 1 ? "checkbox" : "radio");
		$list_values_to_display = static::get_list_values_to_display($property);
		
		$cp_options = array();
		if (!empty($property->cp_options)) {
			$cp_options = encoding_normalize::json_decode($property->cp_options, true);
		}
		$nb_per_line = 3;
		if (!empty($cp_options) && !empty($cp_options["CHECKBOX_NB_ON_LINE"]) && !empty($cp_options["CHECKBOX_NB_ON_LINE"][0]["value"])) {
			$nb_per_line = $cp_options["CHECKBOX_NB_ON_LINE"][0]["value"];
		}
		
		$values = array();
		if (empty($datas)) {
			$datas = array();
		}
		foreach($datas as $key=>$data){
			$formated_value = $data->get_formated_value();
			if (is_array($formated_value)) {
				$values = array_merge($values, $formated_value);
			} else {
				$values[] = $formated_value;
			}
		}
		$search = array(
				"!!radio_or_checkbox!!",
				"!!onto_row_content_value!!",
				"!!onto_checked!!",
				"!!onto_row_content_label!!",
				"!!onto_row_content_value_index!!"
		);
		$i = 0;
		$inside_row = "<table><tr>";
		foreach($options_values as $id => $value){
			if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
				continue;
			}
			if ($i && !($i%$nb_per_line)) {
				$inside_row.= "</tr><tr>";
			}
			$replace = array(
					$radio_or_checkbox,
					htmlentities($id, ENT_QUOTES, $charset),
					(in_array($id, $values) ? 'checked="checked"' : ''),
					htmlentities($value, ENT_QUOTES, $charset),
					$i
			);
			$inside_row.= "<td>".str_replace($search, $replace, $ontology_tpl["form_row_content_list_checkbox_option"])."</td>";
			$i++;
		}
		$inside_row.= "<tr></table>";
		
		$inside_row.= $ontology_tpl['form_row_content_type'];
		$inside_row.= $ontology_tpl['form_row_content_list_checkbox'];
		$inside_row = str_replace("!!onto_row_content_values!!", implode(",", $values), $inside_row);
		
		$inside_row = str_replace("!!onto_row_content_range!!", $property->range[0], $inside_row);
		
		return $inside_row;
	}
	
	/**
	 * A d�river pour filtrer la liste des valeurs � afficher dans le s�lecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return array();
	}

} // end of onto_common_datatype_ui