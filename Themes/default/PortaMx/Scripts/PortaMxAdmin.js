/**
* \file PortaMxAdmin.js
* Common Javascript functions
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

// show the language assigned title input
function setTitleLang(elm, id)
{
	var curlangid = "curlang" + (id ? id : "");
	var idx = elm.selectedIndex;
	var show = elm.options[idx].value;
	var hide = document.getElementById(curlangid).value;
	document.getElementById(hide + (id ? "_" + id : "")).style.display = "none";
	document.getElementById(show + (id ? "_" + id : "")).style.display = "";
	document.getElementById(curlangid).value = show;
}

// set the title align
function setAlign(IDpref, align)
{
	var old_active = "img" + IDpref + document.getElementById("titlealign" + IDpref).value;
	var new_active = "img" + IDpref + align;
	document.getElementById("titlealign" + IDpref).value = align;
	document.getElementById(old_active).style.backgroundColor = "";
	document.getElementById(new_active).style.backgroundColor = "#e02000";
}

// show the title icon if not empty
function setTitleIcon(elm, id)
{
	var idx = elm.selectedIndex;
	var idx_val = elm.options[idx].value;
	var oldico = document.getElementById("pmxttlicon" + id).src;
	var url = oldico.substring(0, oldico.lastIndexOf("/")+1) + (idx_val == "" ? "none.gif" : idx_val);
	document.getElementById("pmxttlicon" + id).src = url;
}

// check if the header can collapse
function checkCollapse(elm)
{
	var idx = elm.selectedIndex;
	var elm_name = elm.name;
	var idx_val = elm.options[idx].value;
	if(elm_name == "config[visuals][header]")
	{
		if(idx_val == "none")
			document.getElementById("collapse").disabled = true;
		else
			document.getElementById("collapse").disabled = false;
	}
}

// check if the maxheigt field enabled
function checkMaxHeight(elm)
{
	var idx = elm.selectedIndex;
	var idx_val = elm.options[idx].value;
	if(idx_val == "")
	{
		document.getElementById("maxheight").style.backgroundColor = "#8898b0";
		document.getElementById("maxheight_sel").style.backgroundColor = "#8898b0";
		document.getElementById("maxheight").disabled = true;
		document.getElementById("maxheight_sel").disabled = true;
	}
	else
	{
		document.getElementById("maxheight").style.backgroundColor = "";
		document.getElementById("maxheight_sel").style.backgroundColor = "";
		document.getElementById("maxheight").disabled = false;
		document.getElementById("maxheight_sel").disabled = false;
	}
}

// check can head, top, bottom, foot sizeinput enabled
function checkSizeInput(elm, side)
{
	var idx = elm.selectedIndex;
	document.getElementById("pmx_size_" + side).disabled = idx == 0;
	document.getElementById("pmx_size_" + side).style.backgroundColor = (idx == 0 ? "#8898b0" : "");
}

// check if pmxcache enabled
function checkPmxCache(elm, val)
{
	if(elm.checked == false)
	{
		document.getElementById("cacheval").style.backgroundColor = "#8898b0";
		document.getElementById("cacheval").value = 0;
		document.getElementById("cache_value").value = 0;

	}
	else
	{
		document.getElementById("cacheval").style.backgroundColor = "";
		document.getElementById("cacheval").value = val;
		document.getElementById("cache_value").value = 1;
	}
}

// Toggle Detailed filelist
function ToggleFileList(elm)
{
	var show = document.getElementById(elm).style.display;
	document.getElementById(elm).style.display = (show == "" ? "none" : "");
}

// check blockpos if changed
function checkBlockPos(elm, cVal)
{
	if(cVal != elm.value)
		document.getElementById("set_savepos").value++;
	else if(document.getElementById("set_savepos").value > 0)
		document.getElementById("set_savepos").value--;
}

// numeric field check
function check_numeric(elm, multchr)
{
	var elm_value = elm.value;
	if(elm_value.length > 0)
	{
		if(multchr == '*')
			var Check = /([\*0-9])+/;
		else if(multchr == ',')
			var Check = /([\,0-9])+/;
		else if(multchr == '.')
			var Check = /([\.0-9])+/;
		else
			var Check = /([0-9])+/;

		if(!elm_value.match(Check) || elm_value.match(Check)[0] != elm_value)
			elm.value = (elm_value.match(Check) ? elm_value.match(Check)[0] : '');
	}
}

// name input check
function check_requestname(elm)
{
	var elm_value = elm.value;
	if(elm_value.length > 0)
	{
		var legal_chars = /([\.\-\_a-zA-Z0-9])+/;
		var check = elm_value.match(legal_chars);
		if(check == null)
			elm.value = ''
		else if(elm_value != check[0])
		{
			var bad_pos = elm_value.match(legal_chars)[0].length;
			var bad_char = elm_value.slice(bad_pos, bad_pos +1);
			elm.value = elm_value.replace(bad_char, '');
			// move cursor back to last pos
			if(elm.setSelectionRange)
			{
				elm.focus();
				elm.setSelectionRange(bad_pos, bad_pos);
			}
			else if (elm.createTextRange) {
				var range = elm.createTextRange();
				range.collapse(true);
				range.moveEnd('character', bad_pos);
				range.moveStart('character', bad_pos);
				range.select();
			}
		}
	}
}

// Toggle all checkboxes
function ToggleCheckbox(elm, what, init)
{
	if(what == "xsel")
		var sides = ["head", "left", "right", "top", "bottom", "foot"];	// xbars
	else
		var sides = ["head", "left", "right", "top", "bottom", "foot", "front", "pages"];	// Panel moderate
	var ischeck = false;
	var ico = elm.src;
	var url = ico.substring(0, ico.lastIndexOf("/")+1);
	for(var i = 0; i < sides.length; i++)
		ischeck = (document.getElementById(what +  sides[i]).checked == true ? true : ischeck);
	if(init == 0)
	{
		ischeck = !ischeck;
		for(var i = 0; i < sides.length; i++)
			document.getElementById(what + sides[i]).checked = ischeck;
	}
	elm.src = url + (ischeck ? "bullet_minus.gif" : "bullet_plus.gif");
}

// Toggle Help messages
function Toggle_help(elm)
{
	var stat = document.getElementById(elm).style.display;
	document.getElementById(elm).style.display = (stat == "none" ? "" : "none");
}

// Showe Help messages
function Show_help(elm, txtdir)
{
	if(txtdir)
		txtdir = '_'+ txtdir;
	else
		txtdir = '';
	var match = /(\S+)/;
	match.exec(document.getElementById(elm).className);
	document.getElementById(elm).className = (RegExp.$1 == "info_frame" ? "info_text"+ txtdir +" plainbox" : "info_frame");
}

// check the request name field
function FormFuncCheck(elm, id)
{
	if(document.getElementById('check.name').value == '')
		alert(document.getElementById('check.name.error').innerHTML);
	else
		FormFunc(elm, id)
}

// common func on forms
function FormFunc(Func, Val, Msg)
{
	if(Msg)
	{
		if(!confirm(Msg) == true)
		return;
	}
	if(Func == "save_pos")
		Val = document.getElementById("set_savepos").value;
	StartProgress();
	PmX_RichEdit_Submit();
	document.getElementById("common_field").name = Func;
	document.getElementById("common_field").value = Val;
	document.getElementById("pmx_form").submit();
}

// dummy func for progressbar
function StartProgress()
{};

// dummy func for bbc_script block
function PmX_RichEdit_Submit()
{};

// the multiple selection show/hide toggle object
function MultiSelect(action_name)
{
	// check Browser type
	this.isMSIE = navigator.appVersion.indexOf("MSIE") > 0;

	// setup Timer vars
	this.timerClicks = this.isMSIE ? 600 : 400;

	// the action array
	this.actioOpts = [];
	this.action = action_name;
	this.elm = document.getElementById(action_name);

	// self init
	this.init();
}

// called by element change
MultiSelect.prototype.changed = function()
{
	var firstidx = 0;
	var isChg = -1;
	for(var idx = firstidx; idx < this.elm.length; idx++)
	{
		if(this.actioOpts[0]["stat"][idx] != this.elm.options[idx].selected)
		{
			this.actioOpts[0]["stat"][idx] = this.elm.options[idx].selected;
			if(this.actioOpts[0]["chng"][idx] > 0)
				isChg = idx;
			this.actioOpts[0]["chng"][idx]++;
		}
	}

	if(isChg != -1)
	{
		this.elm.options[isChg].selected = false;
		var dat = this.elm.options[isChg].value;
		var txt = this.elm.options[isChg].text;
		var dpos = dat.indexOf("=") +1;
		this.elm.options[isChg].value = dat.substr(0, dpos) + (dat.substr(dpos, 1) == "1" ? "0" : "1");
		this.elm.options[isChg].text = (dat.substr(dpos,1)) == "1" ? "^"+txt : txt.substr(1);
		// only for FF 2
		var selheight = document.getElementById(this.action).offsetHeight;
		document.getElementById(this.action).style.top = ((selheight / (this.elm.length -1)) * isChg) + "px";
		this.elm.options[isChg].selected = true;
		document.getElementById(this.action).style.top = "0px";
	}
	else
		setTimeout("stopTimer(" +this.action+ ")", this.timerClicks);
}

// the init func
MultiSelect.prototype.init = function()
{
	this.actioOpts[0] = {};
	this.actioOpts[0]["stat"] = [];
	this.actioOpts[0]["chng"] = [];
	this.setupArray();
}

// setup the array
MultiSelect.prototype.setupArray = function()
{
	for(var idx = 0; idx < this.elm.length; idx++)
	{
		this.actioOpts[0]["stat"][idx] = this.elm.options[idx].selected;
		this.actioOpts[0]["chng"][idx] = 0;
	}
}

function stopTimer(action)
	{eval(action).setupArray();}
function changed(action)
	{eval(action).changed();}
function ReInitMSel(action)
	{eval(action).init();}
/* eof */
