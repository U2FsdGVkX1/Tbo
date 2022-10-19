/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */
;layui.define(function(e) {
    //console.log()
    layui.use(["admin", "carousel"], function() {
        var e = layui.$
          , t = (layui.admin,
        layui.carousel)
          , a = layui.element
          , i = layui.device();
        e(".layadmin-carousel").each(function() {
            var a = e(this);
            t.render({
                elem: this,
                width: "100%",
                arrow: "none",
                interval: a.data("interval"),
                autoplay: a.data("autoplay") === !0,
                trigger: i.ios || i.android ? "click" : "hover",
                anim: a.data("anim")
            })
        }),
        a.render("progress")
    }),

    e("console", {})
});
