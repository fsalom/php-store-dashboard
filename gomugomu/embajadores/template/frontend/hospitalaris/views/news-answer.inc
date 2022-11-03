<div id="new-add"></div>
<span id="new-comment-comment"></span>

<form action="{comment_action}" method="post">
<div id="new-comment">
	<h1>Añade tu comentario</h1>

	<div class="new-comment-textarea">
		<label for="comment">Comentario</label> <strong title="OBLIGATORIO" class="news-required">*</strong> :<br />
		<textarea name="comment" id="comment"></textarea>
	</div>

	<div class="new-comment-fields">
		<label for="author">Nombre</label> <strong title="OBLIGATORIO" class="news-required">*</strong> :
		<input type="text" name="author" id="author" class="new-comment-input" value="{name}"/>
		<label for="email">Email</label> <strong title="OBLIGATORIO" class="news-required">*</strong> :
		<input type="text" name="email" id="email" class="new-comment-input" value="{mail}"/>
		<label for="web">Web</label>  :
		<input type="text" name="web"  id="web"class="new-comment-input" value="{web}"/>
	</div>

</div>

<input type="button" value="Enviar comentario" id="sendcomment">
</form>
