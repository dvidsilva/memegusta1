function like(post,action,usr,btn){
	if(usr == ''){
		var w = document.getElementById('warning'+post);
		$(w).html("Debes <a href='?f=usr&a=login'>Iniciar Sesi&oacute;n</a> para calificar las publicaciones.");
		return('');
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		img = new XMLHttpRequest();
	}else {// code for IE6, IE5
		img = new ActiveXObject("Microsoft.XMLHTTP");
	}
	img.open('GET','./ajax/like.php?post='+post+'&action='+action+'&usr='+usr,true);
	img.send();
	
	img.onreadystatechange=function(){
		var d = document.getElementById('dislike'+post);	
		var l = document.getElementById('like'+post);
		if (img.readyState==4 && img.status==200){
			var debug = img.responseText;
			var response = img.responseText.split(','); 
			if(response[0] == 1){
				$(l).addClass('selected');
				$(d).removeClass('selected');
			}
			if(response[0] == 0){
				$(d).addClass('selected');
				$(l).removeClass('selected');										
			}
			if(response[2]==1){
				$(l).removeClass('selected');										
				$(d).removeClass('selected');													
			}
			document.getElementById('lc'+post).innerHTML = response[1];//
			//alert(debug);
		}
	}
}
