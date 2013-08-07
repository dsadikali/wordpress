$(document).ready(pageLoaderInit);
function pageLoaderInit(){
  $("a").click(function(event){
    if(this.href.indexOf(home)>=0&&this.href.indexOf('/wp-')<0){
    /*if(this.href.split('?')[1].split('=')[0]=='m'||
       this.href.split('?')[1].split('=')[0]=='p'||
       this.href.split('?')[1].split('=')[0]=='cat'||
       this.href.split('?')[1].split('=')[0]=='page_id'){*/
      // stop default behaviour
      event.preventDefault();
      // remove click border
      this.blur();
      // get caption: either title or name attribute
      var caption = this.title || this.name || "";
      // get rel attribute for image groups
      var group = this.rel || false;
      // display the box for the elements href
      loadPage(this.href);
    }
  });
  document.getElementById('searchform').name='searchform';
  document.searchform.action="javascript:submitSearch('?s='+document.getElementById('s').value)";
}
function getHTTPObject() {
  var xmlhttp;
if (window.XMLHttpRequest) {
  // If IE7, Mozilla, Safari, and so on: Use native object.
  xmlhttp = new XMLHttpRequest();
}
else
{
  if (window.ActiveXObject) {
     // ...otherwise, use the ActiveX control for IE5.x and IE6.
     xmlhttp = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
}
  return xmlhttp;
}
var isWorking = false;
var http = getHTTPObject();

function loadPage(url){
  if(!isWorking){
    scroll(0,0);
    document.getElementById('content').innerHTML='<center><img src="'+loadingIMG.src+'" /></center>';
    http.open('GET',url,true);
    isWorking=true;
    http.onreadystatechange=showPage;
    http.send(null);
  }
}
function submitSearch(param){
  if(!isWorking){
    scroll(0,0);
    document.getElementById('content').innerHTML='<center><img src="'+loadingIMG.src+'" /></center>';
    http.open('GET',window.location+param,true);
    isWorking=true;
    http.onreadystatechange=showPage;
    http.send(null);
  }
}
function showPage(){
  if(http.readyState==4){
    if(http.status==200){
      isWorking=false;
      var content = http.responseText;
      content = content.split('id="content"')[1];
      content = content.substring(content.indexOf('>')+1);
      var depth=1;
      var output='';
      while(depth>0){
        temp = content.split('</div>')[0];
        //count occurrences
        i=0;
        pos = temp.indexOf("<div");
        while(pos!=-1){
          i++;
          pos = temp.indexOf("<div",pos+1);
        }
        //end count
        depth=depth+i-1;
        output=output+content.split('</div>')[0]+'</div>';
        content = content.substring(content.indexOf('</div>')+6);
      }
      document.getElementById('content').innerHTML=output;
      pageLoaderInit();
    }else{
      alert(http.status);
    }
  }
}