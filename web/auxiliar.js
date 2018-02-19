function refresh() {
    vibrar();
	window.location.reload();
}
function vibrar() {
	navigator.vibrate(50);
}
function casa() {
	vibrar();
	location.href="menu.php";
}
