function add_comment(){
	var cuser = document.getElementById('cuser').value;
	if(cuser == ''){
		alert('Debes iniciar sesi&oacute;n para comentar');
		return('');
	}
	var post = document.getElementById('post').value;
	var ncomment = document.getElementById('ncomment').value;
	if(ncomment == ''){
		return('');
	}
	var content = 'content='+ncomment+'&post='+post+'&usr='+cuser;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		img = new XMLHttpRequest();
	}else {// code for IE6, IE5
		img = new ActiveXObject("Microsoft.XMLHTTP");
	}
	img.open('POST','./ajax/comment.php',true);
	img.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	img.setRequestHeader("Content-length", content.length);
//	img.setRequestHeader("Connection", "close");
	img.send(content);

	img.onreadystatechange=function(){
		var img_url = document.getElementById('img_url').value;	
		var username = document.getElementById('username').value;			
		if (img.readyState==4 && img.status==200){
			var r = img.responseText.split(','); 
			if(r[0]=='Y'){
				document.getElementById('ncomment').value = '';
				var d = "<div class='comment' id='comment"+r[1]+"'>"
					d += "<img src='./uploads/profile/"+img_url+"'/>" 
					d += "<span class='username'>"+username+"</span>";
				d += "<p>"+ncomment+"</p>";
				d += "</div>";
				document.getElementById('comments').innerHTML += d;
			}
		}
	}
}
function show_ac(){
	document.getElementById('add_comment').style = 'display:block';
}
