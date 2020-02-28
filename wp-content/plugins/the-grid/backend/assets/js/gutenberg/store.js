/**
 * WordPress Dependencies
 */

const { data, apiFetch } = wp;
const {
	registerStore,
	withSelect,
} = data;

const DEFAULT_STATE = {
	grids   : {
		loading : true
	},
	facets  : {
		loading : true
	},
	loading : true,
};

const actions = {
	setGrids( grids ) {
		return {
			type: 'SET_GRIDS',
			grids,
		};
	},
	fetch( path ) {
		return {
			type: 'FETCH_FROM_API',
			path,
		};
	},
};

export default registerStore( 'the_grid', {
	reducer( state = DEFAULT_STATE, action ) {

		switch ( action.type ) {
			case 'SET_GRIDS':
				return {
					...state,
					grids: action.grids,
				};
		}

		return state;
	},
	actions,
	selectors: {
		getGrids( state ) {
			return state.grids;
		},
	},
	controls: {
		FETCH_FROM_API( action ) {
			return apiFetch( { path: action.path } );
		},
	},
	resolvers: {
		* getGrids( state ) {

			const path  = '/the_grid/v1/get/?type=grids';
			const grids = yield actions.fetch( path );

			return actions.setGrids( grids );

		},
	},
} );
