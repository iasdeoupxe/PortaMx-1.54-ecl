/**
* \file PortaMxPopup.js
* Common Javascript functions
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

// data objects
var	pWind;
var pWindPos;
var oPmxTitle;
var oPmxCats;
var oPmxCatsData;
var oPmxAcs;
var oPmxTitleUnMod = [];
var oPmxAcsUnMod = [];
var oPmxCatsUnMod = [];
var oPmxCatNameUnMod = [];

function InitPopup()
{
	pWind = {};
	pWindPos = {};
	oPmxTitle = [];
	oPmxCats = [];
	oPmxCatsData = [];
	oPmxAcs = [];
}
InitPopup();

// Toggle languages on overview
function pWindToggleLang(pSide)
{
	var side = ((pSide) ? pSide : '');
	var currlang = document.getElementById('pWind.def.lang'+ side).innerHTML;
	var elm = document.getElementById('pWind.lang.sel');
	for(var idx = 0; idx < elm.length; idx++)
	{
		if(elm.options[idx].value == currlang)
			var nextlang = (idx + 1 < elm.length ? elm.options[idx +1].value : elm.options[0].value);
	}
	var allid = document.getElementById('pWind.all.ids'+ side).value.split(' ');
	for(var idx = 0; idx < allid.length; idx++)
		document.getElementById('sTitle.text.'+ allid[idx] + side).innerHTML = pmxHtmlSpecialChars(document.getElementById('sTitle.text.'+ nextlang +'.'+ allid[idx] + side).value);

	document.getElementById('pWind.def.lang'+ side).innerHTML = nextlang;
	document.getElementById('pWind.language'+ side).value = nextlang;
}

// Title Settings popup
function pmxSetTitle(id, pSide)
{
	var side = ((pSide) ? pSide : '');
	pWind = pmxPopupWindow('pmxSetTitle', id, pSide);
	if(pWind)
	{
		// check the need object
		if(!oPmxTitle[id])
			oPmxTitle[id] = {};
		if(!oPmxTitle[id].text)
			oPmxTitle[id].text = {};
		if(!oPmxTitle[id].lang)
			oPmxTitle[id].lang = [];
		if(!oPmxTitleUnMod[id])
			oPmxTitleUnMod[id] = {};

		// Get the input values
		oPmxTitle[id].Language = document.getElementById('pWind.language'+ side).value;
		if(!oPmxTitleUnMod[id].Language)
			oPmxTitleUnMod[id].Language = document.getElementById('pWind.language'+ side).value;
		var elm = document.getElementById('pWind.lang.sel');
		for(var idx = 0; idx < elm.length; idx++)
		{
			oPmxTitle[id].lang[idx] = elm.options[idx].value;
			if(!oPmxTitleUnMod[id].lang)
				oPmxTitleUnMod[id].lang = [];
			oPmxTitleUnMod[id].lang[idx] = elm.options[idx].value;

			oPmxTitle[id].text[oPmxTitle[id].lang[idx]] = document.getElementById('sTitle.text.'+ oPmxTitle[id].lang[idx] +'.'+ id + side).value;
			if(!oPmxTitleUnMod[id].text)
				oPmxTitleUnMod[id].text = {};
			oPmxTitleUnMod[id].text[oPmxTitleUnMod[id].lang[idx]] = document.getElementById('sTitle.text.'+ oPmxTitleUnMod[id].lang[idx] +'.'+ id + side).value;

			elm.options[idx].selected = elm.options[idx].value == oPmxTitle[id].Language;
		}
		oPmxTitle[id].icon = document.getElementById('sTitle.icon.'+ id).value;
		if(!oPmxTitleUnMod[id].icon)
			oPmxTitleUnMod[id].icon = document.getElementById('sTitle.icon.'+ id).value;
		oPmxTitle[id].align = document.getElementById('sTitle.align.'+ id).value;
		if(!oPmxTitleUnMod[id].align)
			oPmxTitleUnMod[id].align = document.getElementById('sTitle.align.'+ id).value;
		if(!oPmxTitle[id].align_keys)
			oPmxTitle[id].align_keys = ['left', 'center', 'right'];

		// Set the values in the Popup
		document.getElementById('pWind.text').value = (oPmxTitle[id].text[oPmxTitle[id].Language] == '???' ? '' : oPmxTitle[id].text[oPmxTitle[id].Language]);
		document.getElementById('pWind.icon').src = document.getElementById('pWind.icon.url').value + oPmxTitle[id].icon;
		for(var i = 0; i < oPmxTitle[id].align_keys.length; i++)
			document.getElementById('pWind.align.'+ oPmxTitle[id].align_keys[i]).style.backgroundColor = '';
		document.getElementById('pWind.align.'+ oPmxTitle[id].align).style.backgroundColor = '#e02000';
		var icons = document.getElementById('pWind.icon_sel');
		for(var i = 0; i < icons.length; i++)
			icons.options[i].selected = (icons.options[i].value == oPmxTitle[id].icon ? true : false);

		pmxPopupShow();
		if(pmx_popup_rtl == true)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos'+ side +'.pmxSetTitle').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// Update Titles
function pmxUpdateTitles()
{
	var id = document.getElementById('pWind.id').value;
	var side = document.getElementById('pWind.side').value;
	oPmxTitle[id].text[oPmxTitle[id].Language] = (document.getElementById('pWind.text').value == '' ? '???' : document.getElementById('pWind.text').value);

	// check if any isModify
	var elm = document.getElementById('pWind.lang.sel');
	var lang;
	var isModify = 0;
	for(var idx = 0; idx < elm.length; idx++)
	{
		lang = elm.options[idx].value;
		isModify +=	(oPmxTitle[id].text[lang] == document.getElementById('sTitle.text.'+ lang +'.'+ id + side).value ? 0 : 1);
	}
	if(isModify > 0)
	{
		var elm = document.getElementById('addnodes'+ side);
		for(var l = 0; l < oPmxTitle[id].lang.length; l++)
		{
			if(!document.getElementById('upd_title.'+ id +'.'+ oPmxTitle[id].lang[l]))
			{
				var newelm = document.createElement('input');
				newelm.id = 'upd_title.'+ id +'.'+ oPmxTitle[id].lang[l];
				if(side == '')
					newelm.name = 'upd_overview[title]['+ id +'][lang]['+ oPmxTitle[id].lang[l] +']';
				else
					newelm.name = 'upd_overview['+ side.substring(1) +'][title]['+ id +'][lang]['+ oPmxTitle[id].lang[l] +']';
				newelm.type = 'hidden';
				elm.appendChild(newelm);
			}
			document.getElementById('upd_title.'+ id +'.'+ oPmxTitle[id].lang[l]).value = (oPmxTitle[id].text[oPmxTitle[id].lang[l]] == '???' ? '' : oPmxTitle[id].text[oPmxTitle[id].lang[l]]);
		}
	}

	if(oPmxTitle[id].icon != document.getElementById('sTitle.icon.'+ id).value)
	{
		if(!document.getElementById('upd_titles.'+ id +'.icon'))
		{
			var newelm = document.createElement('input');
			newelm.id = 'upd_icon.'+ id +'.icon';
			if(side == '')
				newelm.name = 'upd_overview[title]['+ id +'][icon]';
			else
				newelm.name = 'upd_overview['+ side.substring(1) +'][title]['+ id +'][icon]';
			newelm.type = 'hidden';
			elm.appendChild(newelm);
		}
		document.getElementById('upd_icon.'+ id +'.icon').value = oPmxTitle[id].icon;
		isModify++;
	}

	if(oPmxTitle[id].align != document.getElementById('sTitle.align.'+ id).value)
	{
		if(!document.getElementById('upd_titles.'+ id +'.align'))
		{
			var newelm = document.createElement('input');
			newelm.id = 'upd_align.'+ id +'.align';
			if(side == '')
				newelm.name = 'upd_overview[title]['+ id +'][align]';
			else
				newelm.name = 'upd_overview['+ side.substring(1) +'][title]['+ id +'][align]';
			newelm.type = 'hidden';
			elm.appendChild(newelm);
		}
		document.getElementById('upd_align.'+ id +'.align').value = oPmxTitle[id].align;
		isModify++;
	}

	// update the overview screen
	var elm = document.getElementById('pWind.lang.sel');
	for(var idx = 0; idx < elm.length; idx++)
		document.getElementById('sTitle.text.'+ elm.options[idx].value +'.'+ id + side).value = oPmxTitle[id].text[elm.options[idx].value];

	var lang = document.getElementById('pWind.language'+ side).value;
	document.getElementById('sTitle.text.'+ id + side).innerHTML = (oPmxTitle[id].text[lang] == '' ? '???' : pmxHtmlSpecialChars(oPmxTitle[id].text[lang]));
	document.getElementById('sTitle.align.'+ id).value = oPmxTitle[id].align;
	document.getElementById('sTitle.icon.'+ id).value = oPmxTitle[id].icon;
	document.getElementById('uTitle.align.'+ id).src = document.getElementById('pWind.image.url').value +'text_align_'+ oPmxTitle[id].align +'.gif';
	document.getElementById('uTitle.icon.'+ id).src = document.getElementById('pWind.icon.url').value + oPmxTitle[id].icon;

	if(isModify > 0)
		pmxRemovePopup('SavePopUpChanges');
	else
		pmxRemovePopup();
}

// Title lang isModify
function pmxChgTitles_Lang(elm)
{
	var id = document.getElementById('pWind.id').value;
	var lang = elm.options[elm.selectedIndex].value;
	oPmxTitle[id].text[oPmxTitle[id].Language] = (document.getElementById('pWind.text').value == '' ? '???' : document.getElementById('pWind.text').value);
	document.getElementById('pWind.text').value = (oPmxTitle[id].text[lang] == '???' ? '' : oPmxTitle[id].text[lang]);
	oPmxTitle[id].Language = lang;
}

// Title align isModify
function pmxChgTitles_Align(key)
{
	var id = document.getElementById('pWind.id').value;
	document.getElementById('pWind.align.'+ oPmxTitle[id].align).style.backgroundColor = '';
	document.getElementById('pWind.align.'+ key).style.backgroundColor = '#e02000';
	oPmxTitle[id].align = key;
}

// Title icon isModify
function pmxChgTitles_Icon(elm)
{
	var id = document.getElementById('pWind.id').value;
	var cIcon = elm.options[elm.selectedIndex].value;
	document.getElementById('pWind.icon').src = document.getElementById('pWind.icon.url').value + cIcon;
	oPmxTitle[id].icon = cIcon;
}

// Article Filter popup
function pmxSetFilter()
{
	pWind = pmxPopupWindow('pmxSetFilter', 0);
	if(pWind)
	{
		pmxPopupShow();

		if(pmx_popup_rtl == false)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos.pmxSetFilter').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// clear category filter
function pmxSetFilterCatClr()
{
	var elm = document.getElementById('pWind.filter.category');
	for(var i = 0; i < elm.options.length; i++)
		elm.options[i].selected = false;
	document.getElementById('pWind.filter.approved').checked = false;
	document.getElementById('pWind.filter.active').checked = false;
	if(document.getElementById('pWind.filter.myown'))
		document.getElementById('pWind.filter.myown').checked = false;
	if(document.getElementById('pWind.filter.member'))
		document.getElementById('pWind.filter.member').value = '';
}

// Send Articl Filter
function pmxSendFilter()
{
	document.getElementById('set.filter.category').value = '';
	var elm = document.getElementById('pWind.filter.category');
	for(var i = 0; i < elm.options.length; i++)
		if(elm.options[i].selected == true)
			document.getElementById('set.filter.category').value += elm.options[i].value + ',';

	document.getElementById('set.filter.approved').value = (document.getElementById('pWind.filter.approved').checked == true? 1 : 0);
	document.getElementById('set.filter.active').value = (document.getElementById('pWind.filter.active').checked == true ? 1 : 0);
	if(document.getElementById('pWind.filter.myown'))
		document.getElementById('set.filter.myown').value = (document.getElementById('pWind.filter.myown').checked == true ? 1 : 0);
	else
		document.getElementById('set.filter.myown').value = 0;
	if(document.getElementById('pWind.filter.member'))
		document.getElementById('set.filter.member').value = document.getElementById('pWind.filter.member').value;
	else
		document.getElementById('set.filter.member').value = '';

	FormFunc('null', 0);
	pmxRemovePopup();
}

// Category popup
function pmxSetCats(id)
{
	pWind = pmxPopupWindow('pmxSetCats', id);
	if(pWind)
	{
		// check the need object
		if(!oPmxCats[id])
			oPmxCats[id] = {};
		oPmxCats[id].catid = document.getElementById('pWind.catid.'+ id).value;
		if(!oPmxCatsUnMod[id])
			oPmxCatsUnMod[id] = document.getElementById('pWind.catid.'+ id).value;;
		var elm = document.getElementById('pWind.cats.sel');
		for(var i = 0; i < elm.length; i++)
			elm.options[i].selected = (elm.options[i].value == oPmxCats[id].catid ? true : false);
		if(elm.options[elm.selectedIndex])
			oPmxCats[id].catname = elm.options[elm.selectedIndex].text;
		else
			oPmxCats[id].catname = elm.options[0].text;

		var catdata =  document.getElementById('pWind.all.catdata').value.split('{}');
		for(i = 0; i < catdata.length; i++)
		{
			var parts = catdata[i].split('|');
			parts[2] = parts[2].replace(/&lt;/g, '<');
			parts[2] = parts[2].replace(/&gt;/g, '>');
			if(!oPmxCatsData[parts[0]])
				oPmxCatsData[parts[0]] = {};
			oPmxCatsData[parts[0]].level = parts[1];
			oPmxCatsData[parts[0]].parent = parts[2];
			oPmxCatsData[parts[0]].cclass = parts[3];
			oPmxCatsData[parts[0]].cname = parts[4];
		}

		pmxPopupShow();
		if(pmx_popup_rtl == true)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos.pmxSetCatName').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// Update Categorys
function pmxUpdateCats(all)
{
	var isModify = 0;
	var id = document.getElementById('pWind.id').value;
	if(all)
	{
		var allid = document.getElementById('pWind.all.ids').value.split(' ');
		for(var idx = 0; idx < allid.length; idx++)
		{
			if(!oPmxCatsUnMod[allid[idx]])
				oPmxCatsUnMod[allid[idx]] = document.getElementById('pWind.catid.'+ allid[idx]).value;
			isModify = pmxUpdOneCat(id, allid[idx], isModify);
		}
	}
	else
		isModify = pmxUpdOneCat(id, id, isModify);

	if(isModify > 0)
		pmxRemovePopup('SavePopUpChanges');
	else
		pmxRemovePopup();
}

// Update one Category
function pmxUpdOneCat(id, allid, isModify)
{
	if(!document.getElementById('pWind.catid.'+ allid) || oPmxCats[id].catid != document.getElementById('pWind.catid.'+ allid).value)
	{
		document.getElementById('pWind.catid.'+ allid).value = oPmxCats[id].catid;
		var elm = document.getElementById('addnodes');
		if(!document.getElementById('upd_category.'+ allid))
		{
			var newelm = document.createElement('input');
			newelm.id = 'upd_category.'+ allid;
			newelm.name = 'upd_overview[category]['+ allid +']';
			newelm.type = 'hidden';
			elm.appendChild(newelm);
		}
		document.getElementById('upd_category.'+ allid).value = oPmxCats[id].catid;
		document.getElementById('pWind.catname.'+ allid).innerHTML = oPmxCatsData[oPmxCats[id].catid].cname;
		document.getElementById('pWind.catclass.'+ allid).className = oPmxCatsData[oPmxCats[id].catid].cclass;
		document.getElementById('pWind.catclass.'+ allid).title = oPmxCatsData[oPmxCats[id].catid].parent;
		document.getElementById('pWind.catlevel.'+ allid).innerHTML = (oPmxCatsData[oPmxCats[id].catid].level);

		isModify++;
	}
	return isModify;
}

// Change Category
function pmxChgCats(elm)
{
	var id = document.getElementById('pWind.id').value;
	oPmxCats[id].catid = elm.options[elm.selectedIndex].value;
	oPmxCats[id].catname = elm.options[elm.selectedIndex].text;
}

// Create Access popup
function pmxSetAcs(id, pSide)
{
	var side = ((pSide) ? pSide : '');
	pWind = pmxPopupWindow('pmxSetAcs', id, pSide);
	if(pWind)
	{
		// set default checked
		document.getElementById('pWindAcsModeupd').checked = true;

		// check the need object
		if(!oPmxAcs[id])
		{
			oPmxAcs[id] = {};
			oPmxAcs[id].grp = document.getElementById('pWind.acs.grp.'+ id).value.split(' ');
		}
		if(!oPmxAcsUnMod[id])
			oPmxAcsUnMod[id] = document.getElementById('pWind.acs.grp.'+ id).value;

		// set update mode
		if(oPmxAcs[id].mode)
		{
			document.getElementById('pWindAcsMode'+ oPmxAcs[id].mode).checked = true;
			var elm = document.getElementById('pWindAcsGroup');
			for(var i = 0; i < elm.length; i++)
			{
				elm.options[i].value = oPmxAcs[id].grp[i].substring(1);
				elm.options[i].selected = (oPmxAcs[id].grp[i].substring(0, 1) == '+');
				if(elm.options[i].text.substring(0, 1) == '^')
					elm.options[i].text = elm.options[i].text.substring(1);
				if(elm.options[i].selected && oPmxAcs[id].grp[i].substring(oPmxAcs[id].grp[i].indexOf('=')+1) == '0')
					elm.options[i].text = '^'+ elm.options[i].text;
			}
		}
		else
		{
			oPmxAcs[id].mode = '';
			pmxSetAcsMode('upd');
		}

		pmxPopupShow();
		var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos'+ side +'.pmxSetAcs').offsetWidth;
		if(pmx_popup_rtl == false)
			pWind.style.left = (pWind.style.left.replace(/px/, '') - (tdlen + 10)) +'px';
		else
			pWind.style.left = (Math.abs(pWind.style.left.replace(/px/, '')) + 5) +'px';
	}
}

// access handing mode
function pmxSetAcsMode(mode)
{
	var id = document.getElementById('pWind.id').value;

	document.getElementById('pWindAcsMode'+ mode).checked = true;
	if(oPmxAcs[id].mode != mode)
	{
		oPmxAcs[id].mode = mode;
		document.getElementById('pWind.acs.grp.'+ id).value = oPmxAcsUnMod[id];
		oPmxAcs[id].grp =  oPmxAcsUnMod[id].split(' ');
	}

	var elm = document.getElementById('pWindAcsGroup');
	for(var i = 0; i < elm.length; i++)
	{
		elm.options[i].disabled = false;
		elm.options[i].value = oPmxAcs[id].grp[i].substring(1);
		elm.options[i].selected = (oPmxAcs[id].grp[i].substring(0, 1) == '+');
		if(elm.options[i].text.substring(0, 1) == '^')
			elm.options[i].text = elm.options[i].text.substring(1);
		if(elm.options[i].selected && oPmxAcs[id].grp[i].substring(oPmxAcs[id].grp[i].indexOf('=')+1) == '0')
			elm.options[i].text = '^'+ elm.options[i].text;

		if(oPmxAcs[id].mode == 'add')
		{
			elm.options[i].disabled = elm.options[i].selected;
			elm.options[i].selected = false;
		}
		if(oPmxAcs[id].mode == 'del')
		{
			elm.options[i].disabled = (elm.options[i].selected == false);
			elm.options[i].selected = false;
		}
	}
}

// Update Access
function pmxUpdateAcs(all)
{
	var id = document.getElementById('pWind.id').value;
	var side = document.getElementById('pWind.side').value;
	var mode = oPmxAcs[id].mode;

	var isModify = 0;
	if(all)
	{
		var allid = document.getElementById('pWind.all.ids'+ side).value.split(' ');
		for(var i = 0; i < allid.length; i++)
			isModify = pmxUpdateOneAcs(allid[i], mode, isModify);
	}
	else
		isModify = pmxUpdateOneAcs(id, mode, isModify);

	if(isModify > 0)
		pmxRemovePopup('SavePopUpChanges');
	else
		pmxRemovePopup();
}

// update one access
function pmxUpdateOneAcs(id, mode, isModify)
{
	var side = document.getElementById('pWind.side').value;
	var grps = [];
	var grpstr = '';
	var nbrsel = 0;

	var selelm = document.getElementById('pWindAcsGroup');
	for(var i = 0; i < selelm.length; i++)
	{
		if(selelm.options[i].selected == true)
		{
			grps[i] = '+'+ selelm.options[i].value;
			nbrsel = nbrsel + (mode == 'del' ? 0 : 1);
		}
		else
		{
			grps[i] = ':'+ selelm.options[i].value;
			nbrsel = nbrsel + (mode == 'add' && selelm.options[i].disabled == true ? 1 : 0);
			nbrsel = nbrsel + (mode == 'del' && selelm.options[i].disabled == false ? 1 : 0);
		}
		grpstr += (grpstr == '' ? grps[i] : ' '+ grps[i]);
	}

	// show / hide the groups icon
	document.getElementById('pWind.grp.on.'+ id).style.display = (nbrsel > 0 ? '' : 'none');
	document.getElementById('pWind.grp.off.'+ id).style.display = (nbrsel > 0 ? 'none' : '');

	// check isModify
	if(!oPmxAcs[id])
		oPmxAcs[id] = {};
	oPmxAcs[id].grp = document.getElementById('pWind.acs.grp.'+ id).value.split(' ');
	if(!oPmxAcsUnMod[id])
		oPmxAcsUnMod[id] = document.getElementById('pWind.acs.grp.'+ id).value;
	oPmxAcs[id].mode = mode;

	for(var i = 0; i < selelm.length; i++)
	{
		if(mode == 'upd')
			isModify = (grps[i] != oPmxAcs[id].grp[i] ? 1 : isModify);
		else
			isModify = (selelm.options[i].selected == true ? 1 : isModify);
	}
	oPmxAcs[id].grp = grpstr.split(' ');

	var elm = document.getElementById('addnodes'+ side);
	if(!document.getElementById('upd_access.'+ id))
	{
		var newelm = document.createElement('input');
		newelm.id = 'upd_access.'+ id;
		if(side == '')
			newelm.name = 'upd_overview['+ mode +'access]['+ id +']';
		else
			newelm.name = 'upd_overview['+ side.substring(1) +']['+ mode +'access]['+ id +']';
		newelm.type = 'hidden';
		elm.appendChild(newelm);
	}
	document.getElementById('pWind.acs.grp.'+ id).value = grpstr;
	grpstr = '';
	for(var i = 0; i < grps.length; i++)
	{
		if(grps[i].substring(0, 1) == '+')
			grpstr += (grpstr == '' ? '' : ',') + grps[i].substring(1);
	}
	document.getElementById('upd_access.'+ id).value = grpstr;

	return isModify;
}

// Create Info popup
function pmxArticleInfo(id)
{
	var	 newelm;
	var infotxt;
	var infoval;

	if(!document.getElementById('pWind.info.title'))
	{
		newelm = document.createElement('span');
		newelm.id = 'pWind.info.title';
		document.getElementById('pWind.title').appendChild(newelm);
	}
	document.getElementById('pWind.info.title').innerHTML = document.getElementById('pWind.title.txt').innerHTML.replace(/%s/, id);

	var replaces = ['<strong>', '</strong>'];
	var infos = document.getElementById('pWind.info.names').value.split(' ');
	var elm = document.getElementById('pWind.info.text');
	var i = elm.childNodes.length;
	while(i > 0)
	{
		i--;
		elm.removeChild(elm.childNodes[i]);
	}

	if(document.getElementById('pWind.ctype.'+ id).value != 'php')
	{
		if(!document.getElementById('pWind.preview'))
		{
			newelm = document.createElement('span');
			newelm.id = 'pWind.preview';
			elm.appendChild(newelm);
			document.getElementById('pWind.preview').innerHTML = document.getElementById('pWind.preview.cmd').innerHTML.replace('[00]', id);
			if(!document.getElementById('pWind.hrline'))
			{
				newelm = document.createElement('hr');
				newelm.id = 'pWind.hrline';
				elm.appendChild(newelm);
			}
		}
	}

	for(var i = 0; i < infos.length; i++)
	{
		infoval = document.getElementById(infos[i] +'.'+ id).innerHTML.split('[|]');
		for(var r = 0; r < replaces.length; r++)
			infoval[0] = infoval[0].replace(replaces[r], '');
		if(infoval[0] == '0' && infoval[1] == '0')
			infotxt = document.getElementById('pWind.'+ infos[i] +'.not.txt').innerHTML;
		else
		{
			if(infoval[0] == '1')
				infotxt = document.getElementById('pWind.'+ infos[i] +'.txt').innerHTML.replace(/%s/, infoval[1]);
			else
			{
				infotxt = document.getElementById('pWind.'+ infos[i] +'.txt').innerHTML.replace(/%s/, infoval[0]);
				infotxt = infotxt.replace(/%s/, infoval[1]);
			}
		}

		if(!document.getElementById('pWind.'+ infos[i]))
		{
			newelm = document.createElement('span');
			newelm.id = 'pWind.'+ infos[i];
			elm.appendChild(newelm);
		}
		document.getElementById('pWind.'+ infos[i]).innerHTML = infotxt;
	}

	pWind = pmxPopupWindow('pmxArticleInfo', id);
	if(pWind)
	{
		pmxPopupShow();
		if(pmx_popup_rtl == true)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos.pmxSetMove').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// Create show arts in cat popup
function pmxShowArt(id)
{
	pWind = pmxPopupWindow('pmxShowArt', id);
	if(pWind)
	{
		document.getElementById('artsorttxt').innerHTML = '';
		document.getElementById('artsort').innerHTML = '';
		document.getElementById('showarts').innerHTML = '';

		var arts = document.getElementById('pWind.catarts.'+ id).value.split('|');
		var sorts = document.getElementById('pWind.artsort.'+ id).value.split('|');
		document.getElementById('artsorttxt').innerHTML = '<b>'+ document.getElementById('pWind.artsorttxt.'+ id).value +'</b>';
		for(var i = 0; i < sorts.length; i++)
			document.getElementById('artsort').innerHTML += '<i>'+ sorts[i] + '</i><br />';
		for(var i = 0; i < arts.length; i++)
			document.getElementById('showarts').innerHTML += arts[i] + '<br />';

		pmxPopupShow();
		if(pmx_popup_rtl == false)
			pWind.style.left = (pWind.style.left.replace(/px/, '') - 50) +'px';
		else
		{
			var tdlen = document.getElementById('pWind.xpos.'+ side +'.pmxSetAcs').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// Create category Move popup
function pmxSetMove(id)
{
	pWind = pmxPopupWindow('pmxSetMove', id);
	if(pWind)
	{
		document.getElementById('pWind.move.catname').innerHTML = '<b>'+ document.getElementById('pWind.move.cat.'+ id).value +'</b>';
		document.getElementById('pWind.move.catname').title = document.getElementById('pmxSetMove.'+ id).title.substring(0, document.getElementById('pmxSetMove.'+ id).title.indexOf(' - '));

		pWind.style.display = '';
		if(pmx_popup_rtl == true)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos.pmxSetMove').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - (tdlen + 30)) +'px';
		}
		else
			pWind.style.left = (Math.abs(pWind.style.left.replace(/px/, '')) + 30) +'px';
	}
}

// Save category Move
function pmxSaveMove()
{
	var id = document.getElementById('pWind.id').value;
	var elm = document.getElementById('pWind.sel.destcat');
	var destid = elm.options[elm.selectedIndex].value;

	if(id == destid)
	{
		if(confirm(document.getElementById('pWind.move.error').value) == true)
			return;
		else
		{
			pmxRemovePopup();
			return;
		}
	}

	var place = 0;
	for(var i = 0; i < 3; i++)
		var place = (document.getElementById('pWind.place.'+ i).checked == true ? document.getElementById('pWind.place.'+ i).value : place);

	var elm = document.getElementById('addnodes');
	if(!document.getElementById('catplace'))
	{
		var newelm = document.createElement('input');
		newelm.id = 'catplace';
		newelm.name = 'catplace';
		newelm.type = 'hidden';
		elm.appendChild(newelm);
	}
	document.getElementById('catplace').value = place;

	if(!document.getElementById('movetocat'))
	{
		var newelm = document.createElement('input');
		newelm.id = 'movetocat';
		newelm.name = 'movetocat';
		newelm.type = 'hidden';
		elm.appendChild(newelm);
	}
	document.getElementById('movetocat').value = destid;

	FormFunc('move_category', id);
	pmxRemovePopup();
}

// Create category Name popup
function pmxSetCatName(id)
{
	pWind = pmxPopupWindow('pmxSetCatName', id);
	if(pWind)
	{
		if(!oPmxCatNameUnMod[id])
			oPmxCatNameUnMod[id] = document.getElementById('pWind.cat.name.'+ id).innerHTML;
		document.getElementById('check.name').value = document.getElementById('pWind.cat.name.'+ id).innerHTML;

		pmxPopupShow();
		if(pmx_popup_rtl == true)
		{
			var tdlen = document.getElementById('pWind.xpos.pmxSetCatName').offsetWidth;
			pWind.style.left = Math.abs(pWind.style.left.replace(/px/, '')) - (tdlen + 55) +'px';
		}
	}
}

// Update category Name
function pmxUpdateCatName()
{
	var id = document.getElementById('pWind.id').value;
	if(document.getElementById('check.name').value != document.getElementById('pWind.cat.name.'+ id).innerHTML)
	{
		if(document.getElementById('check.name').value == '')
			alert(document.getElementById('check.name.error').innerHTML);
		else
		{
			var elm = document.getElementById('addnodes');
			if(!document.getElementById('upd_catname.'+ id))
			{
				var newelm = document.createElement('input');
				newelm.id = 'upd_catname.'+ id;
				newelm.name = 'upd_overview[catname]['+ id +']';
				newelm.type = 'hidden';
				elm.appendChild(newelm);
			}
			document.getElementById('upd_catname.'+ id).value = document.getElementById('check.name').value;
			document.getElementById('pWind.cat.name.'+ id).innerHTML = document.getElementById('check.name').value;

			pmxRemovePopup('SavePopUpChanges');
		}
	}
	else
		pmxRemovePopup();
}

// Article Manager Select ArticleType
function SetpmxArticleType()
{
	pWind = pmxPopupWindow('pmxArticleType', 0);
	if(pWind)
	{
		pmxPopupShow();
		pWind.style.top = (pWindPos.y) +'px';
		if(pmx_popup_rtl == false)
			pWind.style.left = (pWind.style.left.replace(/px/, '') - 50) +'px';
		else
		{
			var tdlen = document.getElementById('pWind.xpos.pmxArticleType').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - (tdlen + 35)) +'px';
		}
	}
}

// Article Manager Send Selected ArticleType
function pmxSendArticleType()
{
	var selm = document.getElementById('pmx.article.type');
	var blocktype = selm.options[selm.selectedIndex].value;

	var elm = document.getElementById('addnodes');
	var newelm = document.createElement('input');
	newelm.id = 'add_new_article';
	newelm.name = 'add_new_article';
	newelm.type = 'hidden';
	elm.appendChild(newelm);
	document.getElementById('add_new_article').value = blocktype;

	FormFunc('', 0);
	pmxRemovePopup();
}

// Block Manager Select BlockType
function SetpmxBlockType(side, sidedesc)
{
	pWind = pmxPopupWindow('pmxBlockType', side, side);
	if(pWind)
	{
		document.getElementById('pWind.title.bar').innerHTML = document.getElementById('pWind.blocktype.title').value.replace(/%s/, sidedesc);

		pmxPopupShow();
		pWind.style.top = (pWindPos.y + 4) +'px';
		if(pmx_popup_rtl == false)
			pWind.style.left = (pWind.style.left.replace(/px/, '') - 50) +'px';
		else
		{
			var tdlen = document.getElementById('pWind.xpos.'+ side +'.pmxSetAcs').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
		}
	}
}

// Block Manager Send Selected BlockType
function pmxSendBlockType()
{
	var side = document.getElementById('pWind.side').value;
	var selm = document.getElementById('pmx.block.type');
	var blocktype = selm.options[selm.selectedIndex].value;

	var elm = document.getElementById('addnodes.'+ side);
	var newelm = document.createElement('input');
	newelm.id = 'add_new_block';
	newelm.name = 'add_new_block['+ side +']';
	newelm.type = 'hidden';
	elm.appendChild(newelm);
	document.getElementById('add_new_block').value = blocktype;

	FormFunc('', 0);
	pmxRemovePopup();
}

// Article Manager RowMove
function pmxArtMove(id, name)
{
	pWind = pmxPopupWindow('pmxRowMove', id);
	if(pWind)
	{
		document.getElementById('pWind.move.pos').innerHTML = '[<b>'+ id +'</b>] '+ name +'';
		document.getElementById('pWind.place.1').checked = true;

		pmxPopupShow();
		if(pmx_popup_rtl == true)
		{
			var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos.pmxRowMove').offsetWidth;
			pWind.style.left = (pWind.style.left.replace(/px/, '') - (tdlen + 30)) +'px';
		}
		else
			pWind.style.left = (Math.abs(pWind.style.left.replace(/px/, '')) + 30) +'px';
	}
}

// Aricle Manager Send RowMove
function pmxSendArtMove()
{
	var id = document.getElementById('pWind.id').value;

	for(var i = 0; i < 2; i++)
		var place = (document.getElementById('pWind.place.'+ i).checked == true ? document.getElementById('pWind.place.'+ i).value : place);

	var rowpos = [];
	var selelm = document.getElementById('pWind.sel')
	for(var i = 0; i < selelm.length; i++)
	{
		if(selelm.options[i].selected == true)
			var iSel = i;
	}

	// check..
	var toID = selelm.options[iSel].value;
	if(toID == id)
	{
		if(confirm(document.getElementById('pWind.move.error').value) == true)
			return;
		else
		{
			pmxRemovePopup();
			return;
		}
	}

	var elm = document.getElementById('addnodes');
	if(!document.getElementById('upd_rowpos'))
	{
		var newelm = document.createElement('input');
		newelm.id = 'upd_rowpos';
		newelm.name = 'upd_rowpos';
		newelm.value = id +','+ place +','+ toID;
		newelm.type = 'hidden';
		elm.appendChild(newelm);
	}

	FormFunc('', 0);
	pmxRemovePopup();
}

// Block Manager RowMove
function pmxRowMove(id, side)
{
	var allid = document.getElementById('pWind.all.ids.'+ side).value.split(' ');
	if(allid.length > 1)
	{
		pWind = pmxPopupWindow('pmxRowMove', id, side);
		if(pWind)
		{
			document.getElementById('pWind.move.blocktyp').innerHTML = '[<b>'+ document.getElementById('pWind.pos.'+ side +'.'+ id).innerHTML +'</b>] '+ document.getElementById('pWind.desc.'+ side +'.'+ id).innerHTML;
			document.getElementById('pWind.desc.'+ side +'.'+ id).innerHTML;
			document.getElementById('pWind.sel.'+side).style.display = '';
			document.getElementById('pWind.place.1').checked = true;

			pmxPopupShow();
			if(pmx_popup_rtl == true)
			{
				var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos'+ side +'.pmxRowMove').offsetWidth;
				pWind.style.left = (pWind.style.left.replace(/px/, '') - (tdlen + 30)) +'px';
			}
			else
				pWind.style.left = (Math.abs(pWind.style.left.replace(/px/, '')) + 30) +'px';
		}
	}
}

// Block Manager Send RowMove
function pmxSendRowMove()
{
	var id = document.getElementById('pWind.id').value;
	var side = document.getElementById('pWind.side').value;

	for(var i = 0; i < 2; i++)
		var place = (document.getElementById('pWind.place.'+ i).checked == true ? document.getElementById('pWind.place.'+ i).value : place);

	var rowpos = [];
	var selelm = document.getElementById('pWind.sel.'+ side)
	for(var i = 0; i < selelm.length; i++)
	{
		if(selelm.options[i].selected == true)
			var iSel = i;
		rowpos[selelm.options[i].value] = selelm.options[i].text.substr(1, selelm.options[i].text.indexOf(']')-1);
	}
	var toID = selelm.options[iSel].value;

	if(rowpos[toID] < rowpos[id] && place == 'after')
		var toID = selelm.options[iSel +1].value;
	if(rowpos[toID] > rowpos[id] && place == 'before')
		var toID = selelm.options[iSel -1].value;

	// check..
	var delta = Math.abs(rowpos[toID] - rowpos[id]);
	if(Math.abs(rowpos[toID] - rowpos[id]) == 0)
	{
		if(confirm(document.getElementById('pWind.move.error').value) == true)
			return;
		else
		{
			pmxRowMove_RemovePopup();
			return;
		}
	}

	var elm = document.getElementById('addnodes.'+ side);
	if(!document.getElementById('upd_rowpos.'+ id))
	{
		var newelm = document.createElement('input');
		newelm.id = 'upd_rowpos.'+ id;
		newelm.name = 'upd_rowpos['+ side +'][rowpos]';
		newelm.type = 'hidden';
		elm.appendChild(newelm);
	}
	document.getElementById('upd_rowpos.'+ id).value = id +','+ place +','+ toID;

	FormFunc('', 0);
	pmxRowMove_RemovePopup();
}

// Block Manager Remove RowMove popup
function pmxRowMove_RemovePopup()
{
	var side = document.getElementById('pWind.side').value;
	document.getElementById('pWind.sel.'+side).style.display = 'none';
	pmxRemovePopup();
}

// Block Manager Clone/Move
function pmxSetCloneMove(id, side, wType, blockType)
{
	pWind = pmxPopupWindow('pmxSetCloneMove', id, side);
	if(pWind)
	{
		document.getElementById('title.clone.move').innerHTML = document.getElementById('pWind.txt.'+ wType).value;
		document.getElementById('pWind.clone.move.blocktype').innerHTML = document.getElementById('pWind.desc.'+ side +'.'+ id).innerHTML;
		document.getElementById('pWind.worktype').value = wType;

		var selm = document.getElementById('pWind.sel.sides');
		if(blockType == 'html' || blockType == 'bbc_script' || blockType == 'script' || blockType == 'php')
		{
			if(selm.options[selm.length -1].value != 'articles')
			{
				var addoption = new Option(document.getElementById('pWind.addoption').value, 'articles', false, false);
				selm.options[selm.length] = addoption;
			}
		}
		else
		{
			if(selm.options[selm.length -1].value == 'articles')
				selm.options[selm.length -1] = null;
		}

		pWind.style.display = '';
		var tdlen = pWind.offsetWidth - document.getElementById('pWind.xpos'+ side +'.pmxSetCloneMove').offsetWidth;
		if(pmx_popup_rtl == false)
			pWind.style.left = (pWind.style.left.replace(/px/, '') - tdlen) +'px';
	}
}

// Block Manager send Clone/Move
function pmxSendCloneMove()
{
	var id = document.getElementById('pWind.id').value;
	var side = document.getElementById('pWind.side').value;

	var selm = document.getElementById('pWind.sel.sides');
	var toSide = selm.options[selm.selectedIndex].value;
	var wType = document.getElementById('pWind.worktype').value;

	var elm = document.getElementById('addnodes.'+ side);
	var newelm = document.createElement('input');
	newelm.name = wType +'_block';
	newelm.type = 'hidden';
	newelm.value = id +','+ toSide;
	elm.appendChild(newelm);
	FormFunc('', 0);
	pmxRemovePopup();
}

// Create a Popup window
function pmxPopupWindow(elmname, id, pSide)
{
	var side = ((pSide) ? pSide : '');
	pWind = document.getElementById(elmname);
	if(pWind.style.display != '' && document.getElementById('pWind.name').value == 0)
	{
		document.getElementById('pWind.id').value = id;
		document.getElementById('pWind.side').value = side;
		document.getElementById('pWind.name').value = elmname;
		pWindPos = pmxWindPos('pWind.xpos'+ side +'.'+ elmname, 'pWind.ypos.'+ id);
		pWind.style.top = (pWindPos.y + 22) +'px';
		pWind.style.left = pWindPos.x +'px';

		// fix for chrome z-index bug
		if(is_chrome)
		{
			pWind.style.display = '';
			var yp = pmxWinGetTop();
			window.scrollTo(0, 0);
			window.scrollTo(0, yp);
			pWind.style.display = 'none';
		}

		return pWind;
	}
	else
		return null;
}

// Fix the Admin left panel problem
function pmxPopupShow()
{
	// fix the mainscreen bug on admin left panel
	if(document.getElementById('main_admsection'))
	{
		pWind.style.display = '';
		var wndLo = pWindPos.y + pWind.offsetHeight;
		pWind.style.display = 'none';
		var fp = pmxWindPos(null, 'pmx_form');
		maxLo = fp.y + document.getElementById('pmx_form').offsetHeight -30;
		if(wndLo > maxLo)
			document.getElementById('main_admsection').style.paddingBottom = Math.abs(wndLo - maxLo) +'px';
	}
	pWind.style.display = '';
}

// Remove a Popup window
function pmxRemovePopup(showButtons)
{
	if(document.getElementById('pWind.name').value != '')
	{
		if(document.getElementById('main_admsection'))
			document.getElementById('main_admsection').style.paddingBottom = '0';

		document.getElementById(document.getElementById('pWind.name').value).style.display = 'none';
		document.getElementById('pWind.name').value = '';

		if(document.getElementById('pWind.side') && document.getElementById('pWind.sel.'+ document.getElementById('pWind.side').value))
			document.getElementById('pWind.sel.'+ document.getElementById('pWind.side').value).style.display = 'none';

		if(showButtons)
		{
			if(document.getElementById('pWind.side'))
				showButtons = showButtons + document.getElementById('pWind.side').value;
			var i = 0;
			while(document.getElementsByName(showButtons)[i] && document.getElementsByName(showButtons)[i] != 'undefined')
			{
				document.getElementsByName(showButtons)[i].style.display = '';
				i++;
			}
		}
	}
	InitPopup();
}
window.onunload = pmxRemovePopup;

// Cancel popup changes
function pmxCancelPopup(showButtons, curside)
{
	if(curside)
		var side = '.'+ curside;
	else
		var side = '';

	var allid = document.getElementById('pWind.all.ids'+ side).value.split(' ');
	for(i = 0; i < allid.length; i++)
	{
		var id = allid[i];
		if(oPmxTitleUnMod[id])
		{
			var elm = document.getElementById('pWind.lang.sel');
			for(var idx = 0; idx < elm.length; idx++)
				document.getElementById('sTitle.text.'+ elm.options[idx].value +'.'+ id + side).value = oPmxTitleUnMod[id].text[elm.options[idx].value];

			var lang = document.getElementById('pWind.language'+ side).value;
			if(oPmxTitleUnMod[id].text[lang])
				document.getElementById('sTitle.text.'+ id + side).innerHTML = (oPmxTitleUnMod[id].text[lang] == '' ? '???' : pmxHtmlSpecialChars(oPmxTitleUnMod[id].text[lang]));

			document.getElementById('sTitle.align.'+ id).value = oPmxTitleUnMod[id].align;
			document.getElementById('sTitle.icon.'+ id).value = oPmxTitleUnMod[id].icon;
			document.getElementById('uTitle.align.'+ id).src = document.getElementById('pWind.image.url').value +'text_align_'+ oPmxTitleUnMod[id].align +'.gif';
			document.getElementById('uTitle.icon.'+ id).src = document.getElementById('pWind.icon.url').value + oPmxTitleUnMod[id].icon;
		}

		if(oPmxAcsUnMod[id])
		{
			document.getElementById('pWind.acs.grp.'+ id).value = oPmxAcsUnMod[id];
			if(!oPmxAcs[id])
				oPmxAcs[id] = {};
			oPmxAcs[id].grp =  oPmxAcsUnMod[id].split(' ');
			oPmxAcs[id].mode = 'upd';
			var isModyfy = 0;
			var elm = document.getElementById('pWindAcsGroup');
			for(var l = 0; l < elm.length; l++)
				isModyfy += (oPmxAcs[id].grp[l].substring(0, 1) == '+' ? 1 : 0);

			// show / hide the groups icon
			document.getElementById('pWind.grp.on.'+ id).style.display = (isModyfy > 0 ? '' : 'none');
			document.getElementById('pWind.grp.off.'+ id).style.display = (isModyfy> 0 ? 'none' : '');
		}

		if(oPmxCatsUnMod[id] && document.getElementById('upd_category.'+ id))
		{
			document.getElementById('upd_category.'+ id).value = oPmxCatsUnMod[id];
			document.getElementById('pWind.catname.'+ id).innerHTML = oPmxCatsData[oPmxCatsUnMod[id]].cname;
			document.getElementById('pWind.catclass.'+ id).className = oPmxCatsData[oPmxCatsUnMod[id]].cclass;
			document.getElementById('pWind.catclass.'+ id).title = oPmxCatsData[oPmxCatsUnMod[id]].parent;
			document.getElementById('pWind.catlevel.'+ id).innerHTML = (oPmxCatsData[oPmxCatsUnMod[id]].level);
		}

		if(oPmxCatNameUnMod[id])
			document.getElementById('pWind.cat.name.'+ id).innerHTML = oPmxCatNameUnMod[id];
	}

	var addnodes = document.getElementById('addnodes'+ side);
	while(addnodes.firstChild)
		addnodes.removeChild(addnodes.firstChild);

	var i = 0;
	while(document.getElementsByName(showButtons + side)[i] && document.getElementsByName(showButtons + side)[i] != 'undefined')
	{
		document.getElementsByName(showButtons + side)[i].style.display = 'none';
		i++;
	}
}

// convert specialchar in titlea
function pmxHtmlSpecialChars(text)
{
	text = text.replace(/&/g, "&amp;");
	text = text.replace(/"/g, "&quot;");
	text = text.replace(/'/g, "&#039;");
	text = text.replace(/</g, "&lt;");
	text = text.replace(/>/g, "&gt;");
	return text;
}
/* eof */
