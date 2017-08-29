function displayManagement() {
	if (document.getElementById("sysHeaderBar"))
		document.getElementById("sysHeaderBar").style.marginBottom = "0px";
	
	if (document.getElementById("sysHeaderBarManagement")) {
		document.getElementById("sysHeaderBarManagement").style.display = 'flex';
		document.getElementById("sysHeaderBarManagement").style.display = '-webkit-flex';
		document.getElementById("sysHeaderBarManagement").style.marginBottom = "50px";
	}
	if (document.getElementById("sysHeaderBarCMS")) {
		document.getElementById("sysHeaderBarCMS").style.display = 'none';
		document.getElementById("sysHeaderBarCMS").style.marginBottom = "0px";
	}
}

function displayCMS() {
	if (document.getElementById("sysHeaderBar"))
		document.getElementById("sysHeaderBar").style.marginBottom = "0px";

	if (document.getElementById("sysHeaderBarManagement")) {
		document.getElementById("sysHeaderBarManagement").style.display = 'none';
		document.getElementById("sysHeaderBarManagement").style.marginBottom = "0px";
	}
	if (document.getElementById("sysHeaderBarCMS")) {
		document.getElementById("sysHeaderBarCMS").style.display = 'flex';
		document.getElementById("sysHeaderBarCMS").style.display = '-webkit-flex';
		document.getElementById("sysHeaderBarCMS").style.marginBottom = "50px";
	}
}