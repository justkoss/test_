<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_series_view_serieslist.class.php,v 1.3 2018-06-13 10:34:01 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_series_view_serieslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for serie in series %}
<h3>{{serie.name}}</h3>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos �l�ments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_series_view_serieslist_title"];
		$render_datas['series'] = array();
		if(is_array($datas)){
			foreach($datas as $serie_id){
				$render_datas['series'][] = new authority(0, $serie_id, AUT_TABLE_SERIES);
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_series_view_title']
		);
		$series = array(
			'var' => "series",
			'desc' => $this->msg['frbr_entity_series_view_series_desc'],
			'children' => authority::get_properties(AUT_TABLE_SERIES,"series[i]")
		);
		$format[] = $series;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}