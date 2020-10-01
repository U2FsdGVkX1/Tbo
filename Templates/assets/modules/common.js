/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */
;layui.define(function(e) {
    var i = (layui.$,
    layui.layer,
    layui.laytpl,
    layui.setter,
    layui.view,
    layui.admin);
    i.events.logout = function() {
      location.href = "http://" + window.location.host + "/index.php/Login/ajaxLogout"
    }
    ,
    e("common", {})
});
