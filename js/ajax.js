var $={
    getParmeter:function(data){
        var result="";
        for(var key in data){
            result=result+key+"="+data[key]+"&";
        }
        return result.slice(0,-1);
    },

    ajax:function(obj){
        if(obj==null || typeof obj!="object"){
            return false;
        }
        var type=obj.type || 'POST';
        var url=obj.url || location.pathname;
        var data=obj.data || {};
        data=this.getParmeter(data);
        var success=obj.success || function(){};
    
        var xhr;
        if (window.XMLHttpRequest) {
            xhr=new XMLHttpRequest();
        } else {
          xhr=new ActiveXObject("Microsoft.XMLHTTP");
        }
        if(type=='GET'){
            url=url+"?"+data;
            data=null;
        }
        xhr.open(type,url);
        if(type=="POST"){
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        }

        //xhr.setRequestHeader("Origin","http://www.jessicxin.top:2200");
        xhr.send(data);
        xhr.onreadystatechange=function(){
            if(xhr.status===200 && xhr.readyState===4){
                var result=null;
                var grc=xhr.getResponseHeader("Content-Type");
                if(grc.indexOf("json") != -1){
                    result=JSON.parse(xhr.responseText);
                }
                else if(grc.indexOf("xml") != -1){
                    result=xhr.responseXML;
                }
                else{
                    result=xhr.responseText;
                }
                success(result);
            }
        }
    }
};