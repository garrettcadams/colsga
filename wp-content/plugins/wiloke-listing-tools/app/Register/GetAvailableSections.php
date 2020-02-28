<?php

namespace WilokeListingTools\Register;


trait GetAvailableSections {
	protected function getAvailableSections(){
		$this->aAvailableSections = $this->aAllSections;
		if ( empty($this->aUsedSections) ){
			foreach ($this->aAvailableSections as $sectionKey => $aSection) {
				$this->aAvailableSections[$sectionKey] = $this->parseSection($aSection);
			}
			return true;
		}

		if ( empty($this->aAvailableSections) ){
			return true;
		}

		foreach ($this->aUsedSections as $aUsedSection){
			if ( !isset($aUsedSection['isCustomSection']) || $aUsedSection['isCustomSection'] == 'no' ){
				unset($this->aAvailableSections[$aUsedSection['key']]);
			}
		}

		if ( empty($this->aAvailableSections) ){
			return true;
		}

		foreach ($this->aAvailableSections as $sectionKey => $aSection) {
			$this->aAvailableSections[$sectionKey] = $this->parseSection($aSection);
		}

		return true;
	}
}