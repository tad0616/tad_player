/************************************************************************************************************
Ajax chained select
Copyright (C) 2006  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2006
Owner of DHTMLgoodies.com


************************************************************************************************************/
var ajax = new Array();

function getList(sel)
{
	var countryCode = sel.options[sel.selectedIndex].value;
	document.getElementById('sub_opt').options.length = 0;	// Empty city select box
	if(countryCode.length>0){
		var index = ajax.length;
		ajax[index] = new sack();

		ajax[index].requestFile = 'get_list.php?pcsn='+countryCode;	// Specifying which file to get
		ajax[index].onCompletion = function(){ createCities(index) };	// Specify function that will be executed after file has been found
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function createCities(index)
{
	var obj = document.getElementById('sub_opt');
	eval(ajax[index].response);	// Executing the response from Ajax as Javascript code
}


function getSubCategoryList(sel)
{
	var category = sel.options[sel.selectedIndex].value;
	document.getElementById('dhtmlgoodies_subcategory').options.length = 0;	// Empty city select box
	if(category.length>0){
		var index = ajax.length;
		ajax[index] = new sack();

		ajax[index].requestFile = 'getSubCategories.php?category='+category;	// Specifying which file to get
		ajax[index].onCompletion = function(){ createSubCategories(index) };	// Specify function that will be executed after file has been found
		ajax[index].runAJAX();		// Execute AJAX function
	}
}
function createSubCategories(index)
{
	var obj = document.getElementById('dhtmlgoodies_subcategory');
	eval(ajax[index].response);	// Executing the response from Ajax as Javascript code
}