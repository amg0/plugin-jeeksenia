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


// console.log('eqLogic_id =',eqLogic_id);
// console.log('events =',events);
function type2cls(k,obj) { if (parseInt(obj.type)>2) return 'text-danger' }

arr = $.map(events,function(v,i){
    return $.extend({}, v.trace, { type:v.type });
  })

  html = JeeKSenia.MyArray2Table(
      arr,
      'data',
      ['data','time','event','generator','means'],
      null,
      'jeeksenia-cls',
      'jeeksenia-htmlid',
      false,
      {
        //'id':type2cls,
        'data':type2cls,
        'time':type2cls,
        'event':type2cls,
        'generator':type2cls,
        'means':type2cls,
        'type':type2cls
      }
    );
  //html = MyArray2Table(arr,idcolumn,viscols,caption,cls,htmlid,bResponsive) 
  $("#jeeksenia-events").html( html );
  $(document).ready(function () {
    $('#jeeksenia-events').DataTable();
});