<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><?php _e( "Listing Schema Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
                            <td>
                                <div id="jsoneditor" style="height: 800px;"></div>
                                <input type="hidden" name="<?php echo $this->getOptionFieldName( 'schema_template', '_schema' ); ?>" value="<?php echo htmlspecialchars($this->get_settings('schema_template', '', '_schema')); ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>