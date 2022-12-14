<?php
// +-------------------------------------------------+
// ? 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_author.class.php,v 1.10 2019-08-08 06:42:15 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_author extends cms_module_common_datasource_records_list{

	/*
	 * On d?fini les s?lecteurs utilisable pour cette source de donn?e
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_principal_author"
		);
	}

	/*
	 * R?cup?ration des donn?es de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			if($value['author'] != 0){
			    $records = array();
				$query = "select distinct responsability_notice from responsability where responsability_author = '".($value['author']*1)."' and responsability_notice != '".($value['record']*1)."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->responsability_notice;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}

			if (empty($return['records'])) return false;
			
			$return = $this->sort_records($return['records']);
			$return["title"] = "Du m?me auteur";
			return $return;
		}
		return false;
	}
}