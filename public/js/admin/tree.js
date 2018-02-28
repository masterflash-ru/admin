
var mycookie=[]
var db = [];
var target= [];
var indentPixels= [];
var collapsedWidget= [];
var expandedWidget=[];
var endpointWidget=[];
var fillerimg=[];
var widgetWidth=[];
var widgetHeight=[];
var collapsedImg=[];
var endpointImg=[];
var expandedImg=[];


//аналог htmlspecialchars_decode  в PHP
function htmlspecialchars_decode(text)
{
   var chars = Array("&amp;", "&lt;", "&gt;");
   var replacements = Array("&", "<", ">");
   for (var i=0; i<chars.length; i++)
   {
       var re = new RegExp(chars[i], "gi");
       if(re.test(text))
       {
           text = text.replace(re, replacements[i]);
       }
   }
  return text;
}

function dbRecord(mother,display,URL,indent,statusMsg,title){
/*if (URL.search(/javascript/)>-1)
{URL=URL.replace(/"/,"\\'")
	URL=URL.replace(/(javascript:)(.*)/,"$1")+ escape(URL.replace(/(javascript:)(.*)/,"$2"))
}
*/
	this.mother = mother   
	this.display =htmlspecialchars_decode(display)
	this.URL = URL
	this.indent = indent   
	this.statusMsg = statusMsg
	this.title=title
	return this
}
function setCurrState(setting,tree_name) {
	//; path=/
mycookie[tree_name] = document.cookie = tree_name+"=" + escape(setting)+ '; path=/';
}
function getCurrState(tree_name) {
var label = tree_name+"="
        var labelLen = label.length
        var cLen = mycookie[tree_name].length
        var i = 0
        while (i < cLen) {
                var j = i + labelLen
                if (mycookie[tree_name].substring(i,j) == label) {
                        var cEnd = mycookie[tree_name].indexOf(";",j)
                        if (cEnd ==     -1) {
                                cEnd = mycookie[tree_name].length
                        }
                        return unescape(mycookie[tree_name].substring(j,cEnd))
                }
                i++
        }
        return ""
}

function toggle(n,tree_name) {
	var newString = ""
	var currState = getCurrState(tree_name)
	var expanded = currState.charAt(n) 
	newString += currState.substring(0,n)
	newString += expanded ^ 1 
	newString += currState.substring(n+1,currState.length)
	setCurrState(newString,tree_name) 
}

function getGIF(n, currState,tree_name) {
	var mom = db[tree_name][n].mother  
	var expanded = currState.charAt(n) 
	if (!mom) {
		return endpointWidget[tree_name]
	} else {
		if (expanded == 1) {
			return expandedWidget[tree_name]
		}
	}
	return collapsedWidget[tree_name]
}

function getGIFStatus(n, currState,tree_name)
 {
	var mom = db[tree_name][n].mother  
	var expanded = currState.charAt(n) 
	if (!mom) {	return "No further items"} 
		else {
			if (expanded == 1) 
				{return "Click to collapse nested items"	}
			}
		return "Click to expand nested item"
}
function out(tree_name)
{
var newOutline = ""
var prevIndentDisplayed = 0
var showMyDaughter = 0
var currState = getCurrState(tree_name)
for (var i = 0; i < db[tree_name].length; i++) {
	var theGIF = getGIF(i, currState,tree_name)		
	var theGIFStatus = getGIFStatus(i, currState,tree_name)  	
	var currIndent = db[tree_name][i].indent	
	var expanded = currState.charAt(i) 
	if (currIndent == 0 || currIndent <= prevIndentDisplayed || (showMyDaughter == 1 && (currIndent - prevIndentDisplayed == 1))) {
		newOutline += "<IMG vspace=\"8\" SRC=\""+fillerimg[tree_name]+"\" HEIGHT = 1 WIDTH =" + (indentPixels[tree_name] * currIndent) + ">"
		newOutline += "<A HREF=\"javascript:out('"+tree_name+"')\" " + 	"onMouseOver=\"window.status=\'" + theGIFStatus +	"\';return true;\"  onClick=\"toggle(" + i + ",'"+tree_name+"');return " + 	(theGIF != endpointWidget[tree_name]) + "\">"
		newOutline += "<IMG SRC=\"" + theGIF + "\" HEIGHT=" + widgetHeight[tree_name] + " WIDTH=" + widgetWidth[tree_name] + " BORDER=0></A>"		
		if (db[tree_name][i].URL == "" || db[tree_name][i].URL == null) 
		{
			newOutline += " " + db[tree_name][i].display + "<BR>"	// no link	
		} else {
			newOutline += "<A HREF="+db[tree_name][i].URL+" title=\""+db[tree_name][i].title +"\" onMouseOver=\"window.status=\'" +	db[tree_name][i].statusMsg + "\';return true;\" target=\""+target[tree_name]+"\">" + db[tree_name][i].display + "</A><BR>"
		}
		prevIndentDisplayed = currIndent
		showMyDaughter = expanded
		if (db[tree_name].length > 10000) {document.getElementById(tree_name+'_out').innerHTML=newOutline;newOutline = ""}
	}
}
document.getElementById(tree_name+'_out').innerHTML=newOutline
}


function out_intab(tree_name)
{
var newOutline = '';
var prevIndentDisplayed = 0
var showMyDaughter = 0
var currState = getCurrState(tree_name) 
for (var i = 0; i < db[tree_name].length; i++) 
{newOutline +='<table  cellpadding="0" cellspacing="0" border="0"><tr>';
	var theGIF = getGIF(i, currState,tree_name)		
	var theGIFStatus = getGIFStatus(i, currState,tree_name)  	
	var currIndent = db[tree_name][i].indent	
	var expanded = currState.charAt(i) 
	if (currIndent == 0 || currIndent <= prevIndentDisplayed || (showMyDaughter == 1 && (currIndent - prevIndentDisplayed == 1)))
	 {
		newOutline += "<td><IMG SRC=\""+fillerimg[tree_name]+"\" HEIGHT = 1 WIDTH =" + (indentPixels[tree_name] * currIndent) + "></td>"
		newOutline += "<td><A HREF=\"javascript:out_intab('"+tree_name+"')\" " + 	"onMouseOver=\"window.status=\'" + theGIFStatus +	"\';return true;\"  onClick=\"toggle(" + i + ",'"+tree_name+"');return " + 	(theGIF != endpointWidget[tree_name]) + "\">"
		newOutline += "<IMG SRC=\"" + theGIF + "\" HEIGHT=" + widgetHeight[tree_name] + " WIDTH=" + widgetWidth[tree_name] + " BORDER=0></A></td>"		
		if (db[tree_name][i].URL == "" || db[tree_name][i].URL == null) 
			{
				newOutline += "<td>" + db[tree_name][i].display + "</td></tr>"	// no link	
			} 
			else
			 {
				newOutline += "<td><A HREF="+db[tree_name][i].URL+" title=\""+db[tree_name][i].title +"\" onMouseOver=\"window.status=\'" +	db[tree_name][i].statusMsg + "\';return true;\" target=\""+target[tree_name]+"\">" + db[tree_name][i].display + "</A></td></tr>"
			}
		prevIndentDisplayed = currIndent
		showMyDaughter = expanded
		if (db[tree_name].length > 10000) {document.getElementById(tree_name+'_out').innerHTML=newOutline;newOutline = ""}
	}
newOutline +='</table>';
}

document.getElementById(tree_name+'_out').innerHTML=newOutline
}
