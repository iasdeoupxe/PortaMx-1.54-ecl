/**
* \file PortaMx.js
* Common Javascript functions
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

// PortaMx Opacity Fader
function PmxOpacFader(aOptions)
{
	this.opt = aOptions;
	var elm = document.getElementById(this.opt.fadeContId);
	elm.innerHTML = this.opt.fadeData[this.opt.fadeCsr];
	this.FadeUp(90);
}

PmxOpacFader.prototype.FadeUp = function(start)
{
	if(start == null)
		this.FadeChangeData();
	this.FadeOpacity(start ? start : 0, 100, this.opt.fadeUptime[this.opt.fadeCsr]);
	setTimeout(this.opt.fadeName + '.FadeDown();', this.opt.fadeUptime[this.opt.fadeCsr] + this.opt.fadeHoldtime[this.opt.fadeCsr]);
}

PmxOpacFader.prototype.FadeDown = function()
{
	this.FadeOpacity(100, 0, this.opt.fadeDowntime[this.opt.fadeCsr]);
	setTimeout(this.opt.fadeName + '.FadeUp();', this.opt.fadeDowntime[this.opt.fadeCsr] + this.opt.fadeChangetime);
}

PmxOpacFader.prototype.FadeOpacity = function(opacStart, opacEnd, millisec)
{
	//speed for each frame
	var speed = Math.round(millisec / 100);
	var timer = 0;

	if(opacStart > opacEnd)
	{
		for(var iOpac = opacStart; iOpac >= opacEnd; iOpac--)
		{
			setTimeout(this.opt.fadeName + '.FadeChangeOpac(' + iOpac + ');', timer * speed);
			timer++;
		}
	}
	else if(opacStart < opacEnd)
	{
		for(var iOpac = opacStart; iOpac <= opacEnd; iOpac++)
		{
			setTimeout(this.opt.fadeName + '.FadeChangeOpac(' + iOpac + ');', timer * speed);
			timer++;
		}
	}
}

PmxOpacFader.prototype.FadeChangeOpac = function(iOpac)
{
	var elm = document.getElementById(this.opt.fadeContId).style;
	if(is_ie)
	{
		elm.zoom = 1;
		elm.opacity = (iOpac / 100);
		elm.filter = 'Alpha(Opacity=' + iOpac + ')';
		elm.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + iOpac + ')';
	}
	else
	{
		elm.opacity = (iOpac / 100);
		elm.MozOpacity = (iOpac / 100);
		elm.KhtmlOpacity = (iOpac / 100);
	}
}

PmxOpacFader.prototype.FadeChangeData = function()
{
	this.opt.fadeCsr++;
	if(this.opt.fadeCsr >= this.opt.fadeData.length)
		this.opt.fadeCsr = 0;
	var elm = document.getElementById(this.opt.fadeContId);
	elm.innerHTML = this.opt.fadeData[this.opt.fadeCsr];
	pmx_setCookie(this.opt.fadeName, this.opt.fadeCsr);
}

// Print the block content
function PmxPrintPage(pdir, cid, cttl)
{
	var content = document.getElementById('print'+ cid).innerHTML;
	content = content.replace(/<br>/g, '<br />');
	content = content.replace(/<hr>/g, '<hr />');
	content = content.replace(/<img([^>]*)>/g, '<img$1 />');
	var pmxprint = window.open(window.location.href, 'printer', '');
	pmxprint.document.open();
	pmxprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
	pmxprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" dir="'+ pdir +'">');
	pmxprint.document.write('<head><meta http-equiv="Content-Type" content="text/html" />');
	pmxprint.document.write('<title>Print of "'+ cttl +'"</title>');
	pmxprint.document.write('<link rel="stylesheet" type="text/css" href="'+ smf_default_theme_url +'/PortaMx/SysCss/portamx_print.css" />');
	pmxprint.document.write('<link rel="stylesheet" type="text/css" href="'+ smf_default_theme_url +'/PortaMx/SysCss/portamx.css" /></head>');
	pmxprint.document.write('<body class="pmx_printbody">'+ content +'</body></html>');
	pmxprint.document.close();
}

// get an XML document using XMLHttpRequest.
function pmxXMLrequest(sUrl, sParams)
{
	var sResult = '';
	if(window.XMLHttpRequest)
	{
		var oMyDoc = new XMLHttpRequest();
		if(sParams != null)
		{
			// POST mode
			oMyDoc.open('POST', smf_scripturl +'?'+ sUrl, false);
			oMyDoc.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			oMyDoc.setRequestHeader('Content-length', sParams.length);
			oMyDoc.setRequestHeader('Connection', 'close');
			oMyDoc.send(sParams);
		}
		else
		{
			// GET mode
			oMyDoc.open('GET', smf_scripturl +'?'+ sUrl, false);
			oMyDoc.send(null);
		}

		// get the server result
		if(oMyDoc.readyState == 4 && oMyDoc.status == 200)
			sResult = decodeURIComponent(oMyDoc.responseXML.getElementsByTagName("pmx")[0].textContent);

		oMyDoc = {};
	}
	return sResult;
}

// Submit a static block
function pmx_StaticBlockSub(id, elm, pValue, uid)
{
	if(pValue == 'pg')
	{
		pValue = pmx_getCookie('LSBsub'+ id);
		var cat = pValue.replace(/(\&pg[\[a-zA-Z0-9\=\-\]]+)/g, '');
		var sUrl = decodeURI(elm.href).match(/(pg[\[a-zA-Z0-9\-\]]+).([0-9]+)/);
		pValue = cat +'&'+ sUrl[1] +'='+ sUrl[2];
		pmx_setCookie('LSBsub'+ id, pValue);
	}
	else
		pmx_setCookie('LSBsub'+ id, pValue.replace(/;/g, '&'));

	pmxWinGetTop(uid);
	elm.href = 'javascript:void(0)';
	document.getElementById('pmx_static'+ id +'_data').value = pValue.replace(/\&/g, ';');
	document.getElementById('pmx_static'+ id +'_form').submit();
}

// Toggle the titles for upshrink boxex
function PmxBlock_Toggle(elm, smfToggle, collapse, expand)
{
	eval(smfToggle + ".toggle()");
	var curtitle = elm.title;
	if(curtitle.indexOf(collapse) != -1)
		elm.title = curtitle.replace(collapse, expand);
	else
		elm.title = curtitle.replace(expand, collapse);
	return false;
}

// Toggle the info boxes
function Info_Toggle(elm)
{
	elm2 = elm + "2";
	elm2State = document.getElementById(elm2).style.display;
	document.getElementById(elm2).style.display = document.getElementById(elm).style.display;
	document.getElementById(elm).style.display = elm2State;
}

// expand / collapse a teased html page
var HTMLpagetop;
function ShowHTML(pageid)
{
	var shortid = "short_" + pageid;
	var fullid = "full_" + pageid;
	if(document.getElementById(fullid).style.display == "none")
	{
		document.getElementById("href_"+ fullid).href = document.getElementById("href_"+ shortid);
		document.getElementById(fullid).style.display = ""
		document.getElementById(shortid).style.display = "none"
		document.getElementById("href_"+ shortid).href = "javascript:void(0)";
		HTMLpagetop = pmxWinGetTop();
	}
	else
	{
		document.getElementById("href_"+ shortid).href = document.getElementById("href_"+ fullid);
		document.getElementById(shortid).style.display = ""
		document.getElementById(fullid).style.display = "none"
		document.getElementById("href_"+ fullid).href = "javascript:void(0)";
		pmx_RestoreScrollTop(HTMLpagetop);
	}
}

// expand / collapse a message attaches
function ShowMsgAtt(elm, sID)
{
	var atthref = elm.href;
	elm.href = 'javascript:void(0)';
	elm.style.display = 'none';
	var cstat = document.getElementById(sID).style.display;
	do
		elm = (cstat == 'none' ? elm.nextSibling : elm.previousSibling);
	while(elm.tagName != 'a' && elm.tagName != 'A');
	elm.href = atthref;
	elm.style.display = '';
	document.getElementById(sID).style.display = (cstat == 'none' ? '' : 'none');
	portamx_EqualHeight();
}

// Get window top postion
function pmxWinGetTop(uid)
{
	var ypos = 0;
	if(window.pageYOffset)
		ypos = window.pageYOffset;
	else if(document.body.scrollTop)
		ypos = document.body.scrollTop;
	else if(document.documentElement.scrollTop)
		ypos = document.documentElement.scrollTop;
	if(uid)
		pmx_setCookie('YOfs', ypos);
	return ypos;
}

// not exist image handling
function onPmxImgError(img)
{
	img.src = pmx_failed_image;
	img.alt = pmx_failed_image_text;
	img.title = pmx_failed_image_text;
	img.style.height = '0px';
	img.style.width = '0px';
	img.onerror = null;
	img.complete = true;
	portamx_EqualHeight();
}

// Pmx onLoad fuction
function portamx_onload()
{
	var pmxImgs = document.getElementsByTagName('img');
	for(var i = 0; i < pmxImgs.length; i++)
		pmxImgs[i].onerror = function(){onPmxImgError(this)};

	window.setTimeout('portamx_imgResize()', 50);
}

// image resize
function portamx_imgResize()
{
	var Xsize, Ysize, fact;
	pmxImgFailed = false;
	for(var i = 0; i < pmx_rescale_images.length; i++)
	{
		possibleRows = document.getElementsByName(pmx_rescale_images[i].name);
		for(var j = 0; j < possibleRows.length; j++)
		{
			if(possibleRows[j].complete)
			{
				Xsize = possibleRows[j].width;
				Ysize = possibleRows[j].height;
				fact = ((Xsize > pmx_rescale_images[i].scale || Ysize > pmx_rescale_images[i].scale) ? (Xsize > Ysize ? Xsize : Ysize) : pmx_rescale_images[i].scale);
				possibleRows[j].width = Math.ceil(Xsize * (pmx_rescale_images[i].scale / fact));
				possibleRows[j].height = Math.ceil(Ysize * (pmx_rescale_images[i].scale / fact));
				possibleRows[j].className = 'bbc_img';
			}
			else
				pmxImgFailed = true;
		}
	}
	if(pmxImgFailed)
		window.setTimeout('portamx_imgResize()', 50);

	portamx_EqualHeight(pmxImgFailed);
}

// set div's to equal height
function portamx_EqualHeight()
{
	var modifyRow = [];
	var modifyTypes = [];
	var cName = '';
	var j = 0;
	var parent;
	var possibleRows;

	possibleRows = document.getElementsByTagName('div');
	for(var i = 0; i < possibleRows.length; i++)
	{
		if(possibleRows[i].className.indexOf('pmxEQH') == -1)
			continue;

		possibleRows[i].style.height = null;
		cName = possibleRows[i].className.replace(/pmxEQH/, '');
		if(!modifyRow[cName])
		{
			modifyTypes[modifyTypes.length] = cName;
			modifyRow[cName] = {};
			modifyRow[cName].j = 0;
			modifyRow[cName].jOfs = 0;
			modifyRow[cName].rSplit = 0;
			modifyRow[cName].elm = [];
			modifyRow[cName].height = [];
		}

		modifyRow[cName].elm[modifyRow[cName].j] = possibleRows[i];
		var parent = possibleRows[i];
		while(parent.tagName.toLowerCase() != 'td')
			parent = parent.parentNode;

		if(parent.offsetLeft > 20 && modifyRow[cName].rSplit == 0)
		{
			modifyRow[cName].jOfs = 0;
			modifyRow[cName].rSplit = 1;
		}
		if(!modifyRow[cName].height[modifyRow[cName].jOfs])
			modifyRow[cName].height[modifyRow[cName].jOfs] = 0;

		modifyRow[cName].height[modifyRow[cName].j] = possibleRows[i].offsetHeight > modifyRow[cName].height[modifyRow[cName].jOfs] ? possibleRows[i].offsetHeight : modifyRow[cName].height[modifyRow[cName].jOfs];
		modifyRow[cName].height[modifyRow[cName].jOfs] = modifyRow[cName].height[modifyRow[cName].j];
		modifyRow[cName].j++;
		modifyRow[cName].jOfs++;
	}

	for(var t = 0; t < modifyTypes.length; t++)
	{
		if(modifyRow[modifyTypes[t]])
		{
			for(var i = 0; i < modifyRow[modifyTypes[t]].elm.length; i++)
				modifyRow[modifyTypes[t]].elm[i].style.height = modifyRow[modifyTypes[t]].height[i] +'px';
		}
	}
	pmx_RestoreScrollTop();
}

// called from smc_toggle on expand
function pmxExpandEQH()
{
	window.setTimeout('portamx_EqualHeight()', 100);
	return false;
}

// called from window.resize
function pmxExpandEQHresize()
{
	window.setTimeout('portamx_EqualHeight()', 500);
	return false;
}
window.onresize = pmxExpandEQHresize;

// Restore window top postion
function pmx_RestoreScrollTop(top)
{
	if(typeof(pmx_restore_top) == 'number' || typeof(top) == 'number')
		window.scrollTo(0, (typeof(pmx_restore_top) == 'number' ? pmx_restore_top : top));
	pmx_restore_top = '';
}

// Get the Popup position (X, Y)
function pmxWindPos(sXname, sYname)
{
	var pWindPos = {};
	if(sXname != null)
		pWindPos.x = pmxGetPos(document.getElementById(sXname), 'x');
	if(sYname != null)
		pWindPos.y = pmxGetPos(document.getElementById(sYname), 'y');
	return pWindPos;
}

// Get a elememt position
function pmxGetPos(elem, sPos)
{
	var x = 0, y = 0;

	while((typeof(elem) == 'object') && elem != null  && (typeof(elem.tagName) != 'undefined'))
	{
		if(elem.id != 'main_container' && elem.id != 'main_admsection')
		{
			y += elem.offsetTop;
			x += elem.offsetLeft;
		}
		if(elem.tagName.toUpperCase() == 'BODY')
			break;

		if(typeof(elem) == 'object')
		{
			if(typeof(elem.offsetParent) == 'object')
				elem = elem.offsetParent;
			else
				break;
		}
	}
	if(sPos == 'x')
		return x;
	else
		return y;
}

// xbarkey events
function xBarKeys(Events)
{
	if(pmx_xBarKeys)
	{
		var xKey = 0;
		if(!Events)
			Events = window.event;
		if(Events.ctrlKey)
		{
			if(Events.which)
				xKey = Events.which;
			else
			{
				if(Events.keyCode)
					xKey = Events.keyCode;
			}
			switch(xKey)
			{
				case 37:
					leftPanel.toggle();
					portamx_EqualHeight();
					return false;
				case 38:
					topPanel.toggle();
					return false;
				case 39:
					rightPanel.toggle();
					portamx_EqualHeight();
					return false;
				case 40:
					bottomPanel.toggle();
					return false;
				default:
					return true;
			}
		}
		else if(Events.altKey)
		{
			if(Events.which)
				xKey = Events.which;
			else
			{
				if(Events.keyCode)
					xKey = Events.keyCode;
			}
			switch(xKey)
			{
				case 38:
					headPanel.toggle();
					return false;
				case 40:
					footPanel.toggle();
					return false;
				default:
					return true;
			}
		}
		else
			return true;
	}
	else
		return true;
}
document.onkeydown = xBarKeys;
/* eof */
