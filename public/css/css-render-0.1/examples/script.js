/*
 *  About: CSS Render examples
 *  Version: 2013.11.22
 *  Company: Pleets Applications
 *  Developer: Darío Rivera
 *  E-mail: fermius.us@gmail.com
 *  Copyright: All rights reserverd
 *

  Release Notes

  - none

 */

window.onload = function()
{
	var buttons = document.querySelectorAll('.plts-btn');

	for (var i = buttons.length - 1; i >= 0; i--) {
		buttons[i].onclick = function() {
			var dem = this.parentNode.children[0];
			var aux = dem.className;
			dem.className = "$ dem";
			setTimeout(function(){
				dem.className = aux;
			},100);
		}
	};
}
