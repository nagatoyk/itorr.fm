var dm = function () {
    var dm_list = [],
        interval,
        _width = '',
        starAdd = function () {
            var a = $.D.m('div');
            a.className = 'star';
            a.innerHTML = $('#like').innerHTML;
            $.D.a(a);
            setTimeout(function () {
                a.className = 'star a';
                setTimeout(function () {
                    a.className = 'star min';
                    setTimeout(function () {
                        $.D.d(a)
                    }, 1000)
                }, 2000)
            })
        },
        spanEnd = function () {
            $.D.d(this)
        },
        made = function (T, cb) {
            var newhei = 120,
                oheight,
                randN = 0;
            var wh = function (f) {
                var newhei = (Math.random() * 96);
                for (var o = $.S('#dm span'), l = o.length, i = 0; i < l; i++) {
                    oheight = o[i].top;
                    if (o[i].left + o[i].clientWidth > _width && Math.abs(oheight - newhei) < 3.4) {
                        randN++;
                        return false;
                        wh(f)
                    }
                }
                return newhei;
                f(newhei)
            };
            newhei = false;
            for (var i = 0; newhei == false; i++) {
                newhei = wh();
                if (i > 60) {
                    return setTimeout(function () {
                        made(T, cb);
                    }, 1000);
                    break
                }
            }
            if (T.msg == 'like') {
                return starAdd()
            }
            _width = obj.clientWidth;
            var span = $.D.m('span');
            span.innerHTML = T.msg.enTxt();
            span.left = _width;
            span.top = newhei;
            $.css(span, 'top:' + newhei + '%;');
            $.D.a(obj, span);
            span.addEventListener('webkitAnimationEnd', spanEnd, false);
            span.addEventListener('animationEnd', spanEnd, false);
            if (cb) {
                cb(span)
            }
        },
        step = function () {
            for (var o = $.S('#dm span'), l = o.length, i = 0, T; i < l; i++) {
                T = o[i];
                var _left = T.left - 10;
                if (_left + T.clientWidth > -500) {
                    T.left = _left;
                }
            }
            var t = parseInt(fm.time());
            for (var l = dm_list.length, T, i = 0; i < l; i++) {
                T = dm_list[i];
                if (T.time < t) {
                    var s = dm_list.shift();
                    if (T.time > (t - 1000)) {
                        made(s);
                        break
                    }
                    i--;
                    l--
                }
            }
            interval = setTimeout(step, 100)
        },
        dm = {
            load: function (i) {
                dm_list = [];
                _width = obj.clientWidth;
                obj.innerHTML = '';
                clearTimeout(interval);
                x('x/?a=dm&id=' + i + '&r=' + Math.random(), function (r) {
                    if (r.error) {
                        return alert(r.error)
                    }
                    dm_list = r;
                    interval = setTimeout(step, 100)
                });
                $('form').onsubmit = function () {
                    var msg = this.msg;
                    if (!msg.value) {
                        msg.focus();
                        return false
                    }
                    dm.send(msg.value);
                    msg.value = '';
                    return false
                }
            },
            exit: function () {
                obj.innerHTML = '';
                clearTimeout(interval)
            },
            send: function (text) {
                x('x/?a=dm&id=' + location.hash.match(/\d+/), 'time=' + parseInt(fm.time()) + '&msg=' + encodeURIComponent(text), function (r) {
                    if (r.error) {
                        return alert(r.error)
                    }
                    made(r, function (s) {
                        s.className = 'a'
                    })
                });
                var uname = $('#u').innerHTML.match(/</) ? '未登录' : $('#u').innerHTML;
                var t = uname + ': 在 ' + location.href + ' 说「' + text + '」';
                if (window.cNz) {
                    cNz(t)
                }
                new Image().src = 'http://x.mouto.org/wb/x.php?itorr=' + encodeURIComponent(t)
            }
        };
    var obj = $.D.m('div');
    obj.id = 'dm';
    $.D.a(obj);
    return dm
}();