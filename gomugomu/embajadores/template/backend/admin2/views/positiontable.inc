<div class="demo">
<div id="dialog-form" title="Create new position">
	<div class="validateTips">All form fields are required.</div>
	<form>
	<fieldset>
		<label for="name">Name</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
		<input type="hidden" name="id" id="id" value="{id}" />
	</fieldset>
	</form>
</div>



<button id="create-user">[+] create new position</button>
<!--<a href="#" id="create-user" class="button white" style="padding:10px;">[+] create new zone</a>-->
<br/><br/>
<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0" class="ui-widget ui-widget-content">
<thead>
<tr>
    <th>Name</th>
    <th>Created by</th>
    <th>Options</th>
</tr>
</thead>
<tbody>
{rows}
</tbody>
</table>

