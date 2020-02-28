<?php

namespace WILCITY_ELEMENTOR\Registers;


trait Helpers {
	protected $aCacheTax = array();
	protected $aPostOptions = array();

	protected function getTerms($taxonomy) {
		if ( isset($this->aCacheTax[$taxonomy]) ){
			return $this->aCacheTax[$taxonomy];
		}

		$totals = wp_count_terms($taxonomy);
		if ( $totals > 100 ){
			$this->aCacheTax[$taxonomy] = 'toomany';
			return $this->aCacheTax[$taxonomy];
		}

		$aRawTerms = get_terms(array('taxonomy'=>$taxonomy, 'hide_empty'=>false));

		$options = [ '' => '' ];
		if ( !empty($aRawTerms) && !is_wp_error($aRawTerms) ){
			foreach ( $aRawTerms as $oTerm ) {
				$options[ $oTerm->term_id ] = $oTerm->name;
			}
		}

		$this->aCacheTax[$taxonomy] = $options;
		return $options;
	}

	protected function getPosts($postType, $maxPosts=50){
		if ( isset($this->aPostOptions[$postType]) ){
			return $this->aPostOptions[$postType];
		}

		$query = new \WP_Query(
			array(
				'post_type' => $postType,
				'posts_per_page' => 50,
				'post_status' => 'publish'
			)
		);
		$aOptions = array();
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aOptions[$query->post->ID] = $query->post->post_title;
			}
		}else{
			$aOptions[] = 'No Posts';
		}
		wp_reset_postdata();
		$this->aPostOptions[$postType] = $aOptions;
		return $aOptions;
	}
}