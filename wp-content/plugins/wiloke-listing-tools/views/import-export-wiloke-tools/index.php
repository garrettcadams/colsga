<div id="<?php echo esc_attr('wiloke-import-export-'.esc_attr($postType . '-'.$settingType)); ?>" class="wiloke-import-export-wrapper" data-post-type="<?php echo esc_attr($postType); ?>" data-setting-type="<?php echo esc_attr($settingType); ?>" style="margin-top: 20px;">
	<div :class="formClass">
		<h3 class="ui dividing header">Import/Export Settings</h3>
		<div class="field input">
			<label>Export Data</label>
			<textarea v-model="exportSettings"></textarea>
			<button style="margin-top: 20px;" class="ui button green" @click.prevent="renderExportSetting">Export</button>
		</div>

		<div class="field input">
			<label>Import Data</label>
			<textarea v-model="importSettings"></textarea>
			<button style="margin-top: 20px;" class="ui button red" @click="runImportSettings">Import</button>
            <div style="margin-top: 20px;" class="ui message green" v-if="importSuccessMsg" v-html="importSuccessMsg"></div>
		</div>
	</div>
</div>