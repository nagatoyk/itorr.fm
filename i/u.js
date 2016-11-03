var U = function (b) {
    var c = 'http://api.mouto.org/',
        d,
        a = {
            init: function (e) {
                d = b(e);
                if (b.cookie('sss'))
                    a.sss(b.cookie('sss'));
                else
                    a.getsss();
            },
            getsss: function () {
                b.j(c + 'x/?a=sss&cb={cb}', function (e) {
                    if (!e.sss) {
                        d.innerHTML = '<a id="loginBtn">登录</a>';
                        b('#loginBtn').onclick = function () {
                            a.login();
                            return false
                        };
                        return
                    }
                    b.cookie('sss', e.sss);
                    a.sss(e.sss)
                })
            },
            sss: function (e) {
                if (e) {
                    b.x('http://itorr.sinaapp.com/fm/x/u.php', 'sss=' + e, function (f) {
                        if (f.error)
                            return a.getsss();
                        a.me = f;
                        d.innerHTML = f.name;
                        a.allike()
                    })
                }
            },
            login: function () {
                var g = {'redirect': location.href};
                var f = [];
                for (var e in g)
                    f.push(e + '=' + encodeURIComponent(g[e]));
                f = f.join('&');
                location.href = c + 'login.html#!' + f
            },
            allike: function () {
                b.x('http://itorr.sinaapp.com/fm/x/?a=allike', function (h) {
                    var f = {};
                    if (!h)
                        return a.likes = f;
                    for (var g = 0, e = h.length; g < e; g++)
                        f[h[g]] = true;
                    a.likes = f;
                    a.iflike(location.hash.match(/\d+/) + '', function (i) {
                        b('#like').className = i ? 'a' : ''
                    })
                })
            },
            iflike: function (e, g) {
                if (!a.likes)
                    return g(false);
                g(a.likes[e])
            }
        };
    return a
}(iTorr);
U.init('#u');