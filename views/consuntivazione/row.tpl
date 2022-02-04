		<td>
			<input type="hidden" name="_modified[]"/>
			<input type="hidden" name="id[]" value="{$cons->id}"/>
			<input type="hidden" name="id_utente[]" value="{$cons->id_utente}"/>
			<input type="hidden" name="data[]" value="{$cons->data}"/>
			{$cons->data|date_format:"%d/%m/%Y"}
		</td>
		
		<td>
			<input class="data-type-integer" type="text" name="gen_nuo_con[]" value="{$cons->gen_nuo_con|number_format:0:",":""}" size="2"/>			
		</td>
		<td>
			<input class="data-type-integer" type="text" name="gen_not[]" value="{$cons->gen_not|number_format:0:",":""}" size="2"/>			
		</td>
		<td>
			<input class="data-type-integer" type="text" name="gen_ric_spe[]" value="{$cons->gen_ric_spe|number_format:0:",":""}" size="2"/>			
		</td>		
		<td>
			<input class="data-type-integer" type="text" name="gen_inc[]" value="{$cons->gen_inc|number_format:0:",":""}" size="2"/>			
		</td>		                
		<td>
			<input class="data-type-integer" type="text" name="app_ven[]" value="{$cons->app_ven|number_format:0:",":""}" size="2"/>			
		</td>		
		<td>
			<input class="data-type-integer" type="text" name="app_aff[]" value="{$cons->app_aff|number_format:0:",":""}" size="2"/>			
		</td>		
		
		<td>
			<input class="data-type-integer" type="text" name="app_acq[]" value="{$cons->app_acq|number_format:0:",":""}" size="2"/>
		</td>
		<td>
			<input class="data-type-integer" type="text" name="pro_acq[]" value="{$cons->pro_acq|number_format:0:",":""}" size="2"/>
		</td>
		<td>
			<input class="data-type-integer" type="text" name="pro_acq_col[]" value="{$cons->pro_acq_col|number_format:0:",":""}" size="2"/>
		</td>
		<td>
			<input class="data-type-integer" type="text" name="pro_loc[]" value="{$cons->pro_loc|number_format:0:",":""}" size="2"/>
		</td>		
		<td>
			<input class="data-type-integer" type="text" name="pro_loc_col[]" value="{$cons->pro_loc_col|number_format:0:",":""}" size="2"/>
		</td>				
		<td>
			<input class="data-type-integer" type="text" name="tra_ven[]" value="{$cons->tra_ven|number_format:0:",":""}" size="2"/>
		</td>				
		<td>
			<input class="data-type-integer" type="text" name="tra_aff[]" value="{$cons->tra_aff|number_format:0:",":""}" size="2"/>
		</td>						
		
		
	{*	<td>
			<input class="data-type-deouble" type="text" name="fatturato[]" value="{$cons->fatturato|number_format:2:",":"."}" size="11"/>
		</td>*}