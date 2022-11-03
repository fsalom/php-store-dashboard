
tinyMCE.init({
	mode : "textareas",

 
	theme : "advanced",
	
	plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,fullscreens",
	
	theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,blockquote,image,media,code,fullscreen",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top"
});


$(document).ready(function(){
    $(".news-right-main").corner("10px");
	$(".news-button").corner("10px");
	$("#myTable").tablesorter({widgets: ['zebra']}); 
	 
	$(".template-label").corner({
			  tl: { radius: 6 },
			  tr: { radius: 6 },
			  bl: { radius: 6 },
			  br: { radius: 6 }});
			  
	$(".btn-slide2").click(function(){
		$("#panel2").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});	 

	$(".btn-slide").click(function(){
		$("#panel").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
		
});





        function confirmar( )
        {
            if(confirm( "esta seguro que desea eliminar el registro ?"  ))
                return true;
            else
                return false;
        }


