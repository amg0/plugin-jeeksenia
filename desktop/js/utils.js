/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* Permet la réorganisation des commandes dans l'équipement */

if (typeof String.prototype.format == 'undefined') {
	String.prototype.format = function()
	{
		var args = new Array(arguments.length);

		for (var i = 0; i < args.length; ++i) {
			// `i` is always valid index in the arguments object
			// so we merely retrieve the value
			args[i] = arguments[i];
		}

		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
};

var Amg0Utils = (function() {
  return {
    MyArray2Table:  function (arr,idcolumn,viscols,caption,cls,htmlid,bResponsive,classmap) {
      var html="";
      var idcolumn = idcolumn || 'id';
      var viscols = viscols || [idcolumn];
      var responsive = ((bResponsive==null) || (bResponsive==true)) ? 'table-responsive-OFF' : ''
    
      if ( (arr) && ($.isArray(arr) && (arr.length>0)) ) {
        var display_order = [];
        var keys= Object.keys(arr[0]);
        $.each(viscols,function(k,v) {
          if ($.inArray(v,keys)!=-1) {
            display_order.push(v);
          }
        });
        // $.each(keys,function(k,v) {
        //   if ($.inArray(v,viscols)==-1) {
        //     display_order.push(v);
        //   }
        // });
    
        var bFirst=true;
        html+="<table id='{1}' class='table {2} table-sm table-hover table-striped {0}'>".format(cls || '', htmlid || 'altui-grid' , responsive );
        if (caption)
          html += "<caption>{0}</caption>".format(caption)
        $.each(arr, function(idx,obj) {
          if (bFirst) {
            html+="<thead>"
            html+="<tr>"
            $.each(display_order,function(_k,k) {
              html+="<th style='text-transform: capitalize;' data-column-id='{0}' {1} {2}>".format(
                k,
                (k==idcolumn) ? "data-identifier='true'" : "",
                "data-visible='{0}'".format( $.inArray(k,viscols)!=-1 )
              )
              html+=k;
              html+="</th>"
            });
            html+="</tr>"
            html+="</thead>"
            html+="<tbody>"
            bFirst=false;
          }
          html+="<tr>"
          $.each(display_order,function(_k,k) {
            if ( classmap && classmap[k] && (typeof classmap[k] === "function") )  {
              cls = classmap[k] ( k, obj );
            } else {
              cls = '';
            }
            html+="<td class='"+cls+"'>";
            html+=(obj[k]!=undefined) ? obj[k] : '';
            html+="</td>"
          });
          html+="</tr>"
        });
        html+="</tbody>"
        html+="</table>";
      }
      else
        html +="<div>{0}</div>".format(_T("No data to display"))
    
      return html;
    }
  }
})();


