/*
 * Copyright (C) 2004 Baron Schwartz <baron at sequent dot org>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, version 2.1.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for more
 * details.
 *
 * $Revision: 1.1 $
 */

// Shows or hides the date chooser on the page
function showChooser(obj, inputId, divId, start, end, format, isTimeChooser, isApptpage,preViewDate,afterViewDate) {
    if (document.getElementById) {
        var input = document.getElementById(inputId);
        var div = document.getElementById(divId);
        if (input !== undefined && div !== undefined) {
            if (input.DateChooser === undefined) {
                input.DateChooser = new DateChooser(input, div, start, end, format, isTimeChooser, isApptpage,preViewDate,afterViewDate);
            }
			obj = Date.parseDate(input.value, format);
			if(obj != null) {
				obj.setFullYear(obj.getFullYear() - 0);
			}
            input.DateChooser.setDate(obj);
			if (input.DateChooser.isVisible()) {
                input.DateChooser.hide();
            }
            else {
                input.DateChooser.show();
            }
           
        }
    }
}

// Sets a date on the object attached to 'inputId'
function dateChooserSetDate(inputId, value, date) {
	var input = document.getElementById(inputId);
	if (input !== undefined && input.DateChooser !== undefined) {
        input.DateChooser.setDate(Date.parseDate(value, input.DateChooser._format));
        if (input.DateChooser.isTimeChooser()) {
            var theForm = input.form;
            var prefix = input.DateChooser._prefix;
            input.DateChooser.setTime(
                parseInt(theForm.elements[prefix + 'hour'].options[
                    theForm.elements[prefix + 'hour'].selectedIndex].value),
                parseInt(theForm.elements[prefix + 'min'].options[
                    theForm.elements[prefix + 'min'].selectedIndex].value));
                 
        }
        //alert(input.DateChooser.isApptpage());
        if(this.checkCurrentDate(input.DateChooser)){
        	/*
        	*	on copy booking page not have hidden date.
        	*/
        	//set date on interface example : 12 Aug  09
	      	input.value = input.DateChooser.getValue();
	      	
	      	//set hidden date on appoiontment page.
	      	if(inputId=="date"){document.getElementById('hidden_date').value=date;}
	      	
	      	//set hidden date on product for sale page.
	       	if(inputId=="cs[saledate]"){document.getElementById('cs[hidden_saledate]').value=date;}
	       	
	       	//set hidden date on booing page.
	       	if(inputId=="cs[apptdate]"){document.getElementById('cs[hidden_apptdate]').value=date;}
	       	
	       	//set cancel hidden date on booking and product for sale.
			if(inputId=="cc[date]"){document.getElementById('cc[hidden_date]').value=date;}
	
	      	//set hidden date on manage booking page - customer birthday.
	      	var indivisheet = inputId.replace("tw[","");
	      	indivisheet = indivisheet.replace("][csbday]","");
	      	if(inputId=="tw["+indivisheet+"][csbday]"){
	      		document.getElementById("tw["+indivisheet+"][hidden_csbday]").value=date.substring(0,4)+"-"+date.substring(4,6)+"-"+date.substring(6);
	      		
	      		// get age from birthdate
	      		var age;
	      		var bday = parseInt(date.substring(6));
	      		var bmonth = parseInt(date.substring(4,6)) - 1;
				var byear=parseInt(date.substring(0,4));
	      		var today = new Date();
				tday = today.getDate();
				tmonth = (today.getMonth());
				tyear = (today.getFullYear());
	      		
	      		if((tmonth > bmonth)||(tmonth == bmonth & tday >= bday)){
	      			age = tyear - byear;
	      		}else{
	      			age = tyear - (byear + 1);
	      		}
	      		if(age<=0){
				alert("Please Check Customer Birth Day Again");
				age="";
				document.getElementById("tw["+indivisheet+"][hidden_csbday]").value="0000-00-00";
				document.getElementById("tw["+indivisheet+"][csbday]").value="";
				}
				document.getElementById("tw["+indivisheet+"][csageinroom]").value = age;
				
	      	}

	    	// if page is appointment page 
	    	if(input.DateChooser.isApptpage){
	    		if(document.getElementById('appointment')!=null){
	    			//when is on date that user can't edit the button add booking and another will gone.
	    			//if user choose date by calenda go to date user can edit the page still not show button.
	    			document.getElementById('appointment').submit();
	    		}
			}
	    }
	    // if page is copy booking page
	  	if(document.getElementById('cpbooking')!=null){
        	document.getElementById('cpbooking').submit();
        }
        input.DateChooser.hide();
	}
     
}
function checkCurrentDate(chooserDate){
	var one_day=1000*60*60*24;
	//var d=new Date("dmY"); 
	var t1= new Date().dateFormat("d/m/Y");
    var t2=	chooserDate._date.dateFormat("d/m/Y");
	var x=t1.split("/");     
    var y=t2.split("/");
    var date1=new Date(x[2],(x[1]-1),x[0]);
    var date2=new Date(y[2],(y[1]-1),y[0])
    var month1=x[1]-1;
    var month2=y[1]-1;
    var result = Math.ceil((date2.getTime()-date1.getTime())/(one_day)); 
    if(chooserDate._afterViewDate!="notCheck" && chooserDate._preViewDate!="notCheck"){
    	if(result<=chooserDate._afterViewDate && result >=0){
	    	if(document.getElementById('errormsg')!=null){
	    		document.getElementById("errormsg").innerHTML="";
	   	 	}
	    	return true;
	    	
	    }else if(chooserDate._preViewDate >= (-result) && result <0){
	   	 	if(document.getElementById('errormsg')!=null){
	   	 		document.getElementById("errormsg").innerHTML="";
	    	}
	    	return true;
	    	
	    }else{
	    	if(document.getElementById('errormsg')!=null){
	    		document.getElementById("errormsg").innerHTML="<img src=\"/images/errormsg.png\" />You don't have permission to access date "+chooserDate._date.dateFormat(chooserDate._format)+" !!";
	    		
	    	}
	    	return false;
	    }
    }else if(chooserDate._afterViewDate==0 && chooserDate._preViewDate==0){
    	//alert("check"+chooserDate._afterViewDate + chooserDate._preViewDate);
    	if(t1 != t2){
    		if(document.getElementById('errormsg')!=null){
    			document.getElementById("errormsg").innerHTML="<img src=\"/images/errormsg.png\" />You don't have permission to access date "+chooserDate._date.dateFormat(chooserDate._format)+" !!";
    		}
    		return false;
    	}else{
    		if(document.getElementById('errormsg')!=null){
    			document.getElementById("errormsg").innerHTML="";
    		}
    		return true;
    	}
    }else{
    	if(document.getElementById('errormsg')!=null){
    		document.getElementById("errormsg").innerHTML="";
    	}
    	return true;
    }
}
// The callback function for when someone changes the pulldown menus on the date
// chooser
function dateChooserDateChange(theForm, prefix) {
    var input = document.getElementById(
        theForm.elements[prefix + 'inputId'].value);
    var newDate = new Date(
        theForm.elements[prefix + 'year'].options[
            theForm.elements[prefix + 'year'].selectedIndex].value,
        theForm.elements[prefix + 'month'].options[
            theForm.elements[prefix + 'month'].selectedIndex].value,
        1);
    // Try to preserve the day of month (watch out for months with 31 days)
    newDate.setDate(Math.max(1, Math.min(newDate.getDaysInMonth(),
                    input.DateChooser._date.getDate())));
    input.DateChooser.setDate(newDate);
    if (input.DateChooser.isTimeChooser()) {
        input.DateChooser.setTime(
            parseInt(theForm.elements[prefix + 'hour'].options[
                theForm.elements[prefix + 'hour'].selectedIndex].value),
            parseInt(theForm.elements[prefix + 'min'].options[
                theForm.elements[prefix + 'min'].selectedIndex].value));
    }
    input.DateChooser.show();
}

// Gets the absolute position on the page of the element passed in
function getAbsolutePosition(obj) {
    var result = [0, 0];
    while (obj != null) {
        result[0] += obj.offsetTop;
        result[1] += obj.offsetLeft;
        obj = obj.offsetParent;
    }
    return result;
}

// DateChooser constructor
function DateChooser(input, div, start, end, format, isTimeChooser, isApptpage,preViewDate,afterViewDate) {
    this._input = input;
    this._div = div;
    this._start = start;
    this._end = end;
    this._format = format;
    this._date = new Date();
    this._isTimeChooser = isTimeChooser;
	this._isApptpage = isApptpage;
	this._preViewDate = preViewDate;
	this._afterViewDate = afterViewDate;
    // Choose a random prefix for all pulldown menus
    this._prefix = "";
    var letters = ["a", "b", "c", "d", "e", "f", "h", "i", "j", "k", "l",
        "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
    for (var i = 0; i < 10; ++i) {
        this._prefix += letters[Math.floor(Math.random() * letters.length)];
    }
}

// DateChooser prototype variables
DateChooser.prototype._isVisible = false;

// Returns true if the chooser is currently visible
DateChooser.prototype.isVisible = function() {
    return this._isVisible;
}

DateChooser.prototype.isApptpage = function() {
    return this._isApptpage;
}

// Returns true if the chooser is to allow choosing the time as well as the date
DateChooser.prototype.isTimeChooser = function() {
    return this._isTimeChooser;
}

// Gets the value, as a formatted string, of the date attached to the chooser
DateChooser.prototype.getValue = function() {
    return this._date.dateFormat(this._format);
}

// Hides the chooser
DateChooser.prototype.hide = function() {
    this._div.style.visibility = "hidden";
    this._div.style.display = "none";
    this._div.innerHTML = "";
    this._isVisible = false;
}

// Shows the chooser on the page
DateChooser.prototype.show = function() {
    // calculate the position before making it visible
    var inputPos = getAbsolutePosition(this._input);
    this._div.style.top = (inputPos[0] + this._input.offsetHeight) + "px";
    this._div.style.left = (inputPos[1] + this._input.offsetWidth) + "px";
    this._div.innerHTML = this.createChooserHtml();
    this._div.style.display = "block";
    this._div.style.visibility = "visible";
    this._div.style.position = "absolute";
    this._isVisible = true;
}

// Sets the date to what is in the input box
DateChooser.prototype.initializeDate = function() {
    if (this._input.value != null && this._input.value != "") {
        this._date = Date.parseDate(this._input.value, this._format);
    }
    else {
        this._date = new Date();
    }
}

// Sets the date attached to the chooser
DateChooser.prototype.setDate = function(date) {
    this._date = date ? date : new Date();
    
}

// Sets the time portion of the date attached to the chooser
DateChooser.prototype.setTime = function(hour, minute) {
    this._date.setHours(hour);
    this._date.setMinutes(minute);
}

// Creates the HTML for the whole chooser
DateChooser.prototype.createChooserHtml = function() {
    var formHtml = "<span class=\"warpDateChooser\">";
    formHtml += "\r\n   <input type=\"hidden\" name=\""
        + this._prefix + "inputId\" value=\""
        + this._input.getAttribute('id') + "\">"
        + "\r\n  <select name=\"" + this._prefix 
        + "month\" onChange=\"dateChooserDateChange(this.form, '"
        + this._prefix + "');\" class=\"dateselect\">";
    for (var monIndex = 0; monIndex <= 11; monIndex++) {
        formHtml += "\r\n    <option value=\"" + monIndex + "\""
            + (monIndex == this._date.getMonth() ? " selected=\"1\"" : "")
            + ">" + Date.monthNames[monIndex] + "</option>";
    }
    formHtml += "\r\n  </select>\r\n  <select name=\""
        + this._prefix + "year\" onChange=\"dateChooserDateChange(this.form, '"
        + this._prefix + "');\" class=\"dateselect\">";
    for (var i = this._start; i <= this._end; ++i) {
        formHtml += "\r\n    <option value=\"" + i + "\""
            + (i == this._date.getFullYear() ? " selected=\"1\"" : "")
            + ">" + (i+0) + "</option>";
    }
    formHtml += "\r\n  </select>";
    formHtml += this.createCalendarHtml();
    if (this._isTimeChooser) {
        formHtml += this.createTimeChooserHtml();
    }
    formHtml += "\r\n  </span>";
    return formHtml;
}

// Creates the extra HTML needed for choosing the time
DateChooser.prototype.createTimeChooserHtml = function() {
    // Add hours
    var result = "\r\n Time:&nbsp;<select name=\"" + this._prefix + "hour\">";
    for (var i = 0; i < 24; ++i) {
        result += "\r\n    <option value=\"" + i + "\""
            + (((this._date.getHours() % 24) == i) ? " selected=\"1\">" : ">")
            + i + "</option>";
    }
    result += "\r\n  </select>";
    // Add minutes
    result += "\r\n  <select name=\"" + this._prefix + "min\">";
    for (var i = 0; i < 60; i ++) {
        result += "\r\n    <option value=\"" + i + "\""
            + ((this._date.getMinutes() == i) ? " selected=\"1\">" : ">")
            + String.leftPad(i, 2, '0') + "</option>";
    }
    result += "\r\n  </select>";
    return result;
}

// Creates the HTML for the actual calendar part of the chooser
DateChooser.prototype.createCalendarHtml = function() {
    var result = "\r\n<table cellspacing=\"0\" class=\"dateChooser\">"
        + "\r\n  <tr><th>S</th><th>M</th><th>T</th>"
        + "<th>W</th><th>T</th><th>F</th><th>S</th></tr>\r\n  <tr>";
    // Fill up the days of the week until we get to the first day of the month
    var firstDay = this._date.getFirstDayOfMonth();
    var lastDay = this._date.getLastDayOfMonth();
    if (firstDay != 0) {
        result += "<td colspan=\"" + firstDay + "\">&nbsp;</td>";
    }
    // Fill in the days of the month
    var i = 0;
    while (i < this._date.getDaysInMonth()) {
        if (((i++ + firstDay) % 7) == 0) {
            result += "</tr>\r\n  <tr>";
        }
        var thisDay = new Date(
            this._date.getFullYear()+0,
            this._date.getMonth(), i);
	
        var js = '"dateChooserSetDate(\''
            + this._input.getAttribute('id') + "', '"
            + thisDay.dateFormat(this._format) + '\',\''+thisDay.dateFormat('Ymd')+'\')';
		
			js += ';"'
        result += "\r\n    <td class=\"dateChooserActive"
            // If the date is the currently chosen date, highlight it
            + (i == this._date.getDate() ? " dateChooserActiveToday" : "")
            + "\" onClick=" + js + ">" + i + "</td>";
         
    }
    // Fill in any days after the end of the month
    if (lastDay != 6) {
        result += "<td colspan=\"" + (6 - lastDay) + "\">&nbsp;</td>";
    }
    return result + "\r\n  </tr>\r\n</table><!--[if lte IE 6.5]><iframe></iframe><![endif]-->";
}
