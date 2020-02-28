<?php

printf(
	'<div style="text-align:center; letter-spacing:1px; clear:both;">
		<a href="%1$s" target="%2$s" style="text-decoration:none;"><span style="color:#666666;">%3$s&nbsp;&nbsp;<font style="color:red;">%4$s</font></span></a>
	</div>',
	esc_url( 'javothemes.com/support/installation/' ), esc_attr( '_blank' ),
	esc_html__( "Event for installation discount and check site setting.", 'jvfrmtd' ),
	esc_html__( "APPLY NOW", 'jvfrmtd' )
);

printf(
	'<div style="text-align:center; letter-spacing:1px; clear:both; margin-top:10px;">
		<a href="%1$s" target="%2$s" style="text-decoration:none;"><span style="color: red; text-align: center; cursor: pointer; font-weight:500; font-size: 14px;">%3$s&nbsp;&nbsp;<font style="color:red; font-weight:600; letter-spacing: 0;">%4$s</font></span></a>
	</div>',
	esc_url( 'https://docs.wpjavo.com/theme/step-by-step-setup/' ), esc_attr( '_blank' ),
	esc_html__( "Check List After Import Demo Data.", 'jvfrmtd' ),
	esc_html__( "Don't Miss it!", 'jvfrmtd' )
);


