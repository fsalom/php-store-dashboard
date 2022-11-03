RewriteRule ^noticias/$ index.php?go=news [L]
RewriteRule ^noticia/([a-zA-Z0-9]+)/([a-zA-Z0-9-]+)$ index.php?go=news&do=view&id=$1 [L,NC]
RewriteRule ^noticia/([a-zA-Z0-9]+)/([a-zA-Z0-9-]+)$#to-comments index.php?go=news&do=view&id=$1#to-comments [L,NC]