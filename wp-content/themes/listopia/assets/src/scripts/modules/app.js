class App {

	constructor() {
		this.el = document.querySelector( '.el' );

		this.listeners();
		this.init();
	}

	init() {
		//Add your init code if you need
	}

	listeners() {
		if ( this.el ) {
			this.el.addEventListener( 'click', this.elClick );
		}
	}

	elClick( e ) {
		e.target.classList.add( 'text-light-grey' );
		e.target.addEventListener( 'transitionend', ( e ) => ( 'color' === e.propertyName ) ? e.target.classList.remove( 'text-light-grey' ) : '' );
	}

}

export default App;
