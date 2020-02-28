<?php
$output_script		= Array();
$output_script[]	= "<script type=\"text/javascript\">";
$output_script[]	= sprintf( "var %s=\"%s\";", 'jvbpd_map_includes_dir'	, '' );
$output_script[]	= sprintf( "var %s=\"%s\";", 'ERR_BED_ADDRESS'			, esc_html__( "Please add address or you added a wrong address", 'jvfrmtd' ) );
$output_script[]	= "</script>";
echo @implode( "\n", $output_script );