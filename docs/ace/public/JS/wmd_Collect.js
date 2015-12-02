/* Example: <script type="text/javascript" id="wmd_collect_id" src="http://www.waimaid.com/Public/JS/wmd_Collect.js?sid=1"></script> */

/*
 ***************************
 *      Ajax Library       *
 ***************************
 */
// creates an XMLHttpRequest instance
function createXMLHttpRequestObject(){
  // xmlHttp will store the reference to the XMLHttpRequest object
  var xmlHttp;
  // try to instantiate the native XMLHttpRequest object
  try{
    // create an XMLHttpRequest object
    xmlHttp = new XMLHttpRequest();
  }
  catch(e){
    // assume IE6 or older
    try{
      xmlHttp = new ActiveXObject("Microsoft.XMLHttp");
    }
    catch(e) { }
  }
  // return the created object or display an error message
  if (!xmlHttp)
    alert("Error creating the XMLHttpRequest object.");
  else 
    return xmlHttp;
}
var collect_request = createXMLHttpRequestObject();

/************************** Process Body **************************/

function getUrlParameterAdv(NAME,URL){

    var url_part = URL.split("?");

    if (url_part.length>1){

        var param = url_part[1].split("&");

        for ( var i=0 ; i<param.length ; i++ ){

            var param_part = param[i].split("=");

            if ( param_part[0] == NAME ){

                if ( param_part.length > 1 ){
                    return param_part[1];
                }else{
                    return "";
                }
            }
        }
    }
    return null;
}

var collect_id_div = document.getElementById("wmd_collect_id");

var collect_site_sid =  getUrlParameterAdv( "sid" , collect_id_div.getAttribute('src') );

var collect_sfid = 'sid=' + encodeURIComponent(collect_site_sid) + '&uv=';
var collect_uv = '0';

var collect_url = 'http://'+window.location.hostname+'/analysis';
// alert(collect_url);
collect_request.open('POST', collect_url , true);
collect_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

if (typeof _cookie( 'wmd_c' ) === "undefined" || _cookie( 'wmd_c' ) === null) {
    var collect_rstr = Math.random().toString(36).substring(7);
    _cookie( 'wmd_c', collect_rstr , { expires: 1, path : '/' });
    // new uv,add pv
    collect_uv = '1';
}

collect_request.send( collect_sfid+collect_uv );