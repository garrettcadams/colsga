<?php

namespace WilokeListingTools\Controllers;


trait PrintSidebarItems {
	public function printSidebarItems(){
		$this->aSections = $this->getAvailableFields();
		?>
		<ul class="list_module__1eis9 list-none">
			<li v-for="oUsedSection in oUsedSections" :class="sidebarClass(oUsedSection.key)"><a class="list_link__2rDA1 text-ellipsis color-primary--hover" :href="sectionID(oUsedSection.key, true)" @click.prevent="scrollTo(oUsedSection.key)"><span class="list_icon__2YpTp"><i :class="oUsedSection.icon"></i></span><span class="list_text__35R07">{{oUsedSection.heading}}</span><span class="list_check__1FbUQ"></span></a></li>
		</ul>
		<?php
	}
}