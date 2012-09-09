/* 
 * This controls the popup style menu's
 */
function menu(){
    $("#nav ul ").css({display: "none"});
    $("#nav li").hover(function(){
        $(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn("def");
        },function(){
        $(this).find('ul:first').css({visibility: "hidden"});
    });
}

function makeDiv(id,cls,where,css){
    if (css == null){ css = "" }
    var nDiv = document.createElement('div');
    nDiv.setAttribute('id', id);
    nDiv.setAttribute('class', cls);
    nDiv.setAttribute('style', css);
    $(where).append(nDiv);
}

function dressAjaxForm(opt){
    // This finds the specified forms with class="ajaxForm"
    // And "Dresses" it with window trimmings etc.
    var ele = $("#"+opt);
    var title = ele.attr('title');
    var id = ele.attr("id");
    ele.prepend("<div id=\"ajaxFormHead\" >"+title+"\
                    <span id=\"ajaxFormClose\" >\
                    <a href=\"#\" \
                    onClick=\"destroyForm();\">X</a>\
                    </span>\
                    </div>");
}

function dressAjaxForms(){
    // This automatically finds all forms with class="ajaxForm"
    // And "Dresses" it with window trimmings etc.
    $(".ajaxForm").each(function(idx){
        var title = $(this).attr('title');
        var id = $(this).attr("id");
        $(this).prepend("<div id=\"ajaxFormHead\" >"+title+"\
                        <span id=\"ajaxFormClose\" >\
                        <a href=\"#\" \
                        onClick=\"$('#reset').click(); $('#"+id+"').hide();\">X</a>\
                        </span>\
                        </div>"
        );
    });
}

function getAdminJSONdata(func, vars, callback){
	// This will grab a JSON object back from adminjson.php
	// there is a required argument called callback
	// callback can represent a function that you want the data to go into or
	// a pseudo-function (ie) function(data){ // code to do something } 
	var returnData = null;
	$.post("adminjson.php?func="+func,vars,function(resp){
		if (resp.status){
			callback(resp.data);
		}else{
			return false;
		}
	},'json');
}
