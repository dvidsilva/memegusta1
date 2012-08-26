function report(post,usr){
	if(usr == '' || post == ''){
		return('');
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		img = new XMLHttpRequest();
	}else {// code for IE6, IE5
		img = new ActiveXObject("Microsoft.XMLHTTP");
	}
	img.open('GET','./ajax/report.php?post='+post+'&usr='+usr,true);
	img.send();
	
	img.onreadystatechange=function(){
		if (img.readyState==4 && img.status==200){
			var response = img.responseText.split(','); 
			var debug = img.responseText;
			if(response[0]==1){
				document.getElementById('report_count'+post).innerHTML = '('+response[1]+')';//
			}
			//alert(debug);
		}
	}
}
