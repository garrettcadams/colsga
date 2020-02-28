/**
 * Tabs
 */
Vue.component('business-hours', {
	data(){
		return {

		};
	},
	template: `
		<div>
			<div class="ui top attached tabular menu">
			  <a class="item" v-for="tab in tabs" :class="{active: tab.isActive}" :data-tab="tab.tabkey">{{tab.name}}</a>
			</div>
			<slot></slot>
		</div>
	`,
	mounted() {
		this.tabs = this.$children;
	}
});
