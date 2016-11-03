/**
 * Created by misaka on 2016-11-3.
 */
String.prototype.enTxt = function () {
    return this.replace(/(^\s*)|(\s*$)/g, '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/ /g, '&nbsp;').replace(/\'/g, '&#39;').replace(/\"/g, '&quot;')
};
var x = function (arg) {
        arg = Array.prototype.slice.apply(arguments);
        if (arg[0].match(/^x\//))
            arg[0] = 'http://itorr.sinaapp.com/fm/' + arg[0];
        return $.x.apply(this, arg)
    },
    fm = function (win, doc) {
        var A = new Audio(),
            list = {},
            run = 0,
            playList = [],
            _img = $('img'),
            _u = function (str) {
                if (str.match(/http:\/\//))
                    return str;
                for (var _a = parseInt(str), _n = str.substr(1), _c = Math.floor(_n.length / _a), _y = _n.length % _a, _s = new Array(), i = 0; i < _y; i++)
                    _s[i] = _n.substr((_c + 1) * i, _c + 1);
                for (i = _y; i < _a; i++)
                    _s[i] = _n.substr(_c * (i - _y) + (_c + 1) * _y, _c);
                for (i = 0, _c = _n = ''; i < _s[0].length; i++)
                    for (j = 0; j < _s.length; j++)
                        _c += _s[j].substr(i, 1);
                for (i = 0, _c = unescape(_c); i < _c.length; i++)
                    _c.substr(i, 1) == '^' ? _n += '0' : _n += _c.substr(i, 1);
                return _n
            },
            add = function () {
                x('nmfm.php?a=random&rid=' + fm.rid + '&_r=' + Math.random(), function (r) {
                    console.log(r[0].xid);
                    for (var l = r.length, i = 0; i < l; i++) {
                        list[r[i].xid] = r[i];
                        playList.push(r[i].xid)
                    }
                    console.log(playList);
                    if ($('#ctrl').className != 'h load') {
                        setTimeout(function () {
                            $('#ctrl').className = '';
                        }, 3e3);
                        $('#next').href = '#!' + playList[0]
                    } else {
                        $('#ctrl').className = ''
                    }
                    if (!run) {
                        fm.next()
                    }
                })
            },
            fm = {
                A: A,
                rid: parseInt(localStorage.getItem('rid') || 0),
                clear: function () {
                    list = {};
                    playList = [];
                    run = 0;
                    add()
                },
                play: function (M) {
                    $('#ctrl').className = 'h';
                    $('#play').className = 'p';
                    if (run && !navigator.userAgent.match(/ip(ad|hone)/i))
                        $('#play2').className = 'h';
                    var vvv;
                    vvv = M.img.match(/http:\/\//) ? M.img : 'http://img.xiami.net/images/album/' + M.img;
                    if (vvv != _img.src) {
                        _img.className = 'h';
                        _img.src = vvv;
                        $('[rel="shortcut icon"]').href = $('[rel="apple-touch-icon-precomposed"]').href = vvv
                    }
                    $('h1').innerHTML = document.title = M.title;
                    $('p').innerHTML = M.artist;
                    /*$('span').innerHTML=M.play;*/
                    A.src = _u(M.mp3);
                    /*new Image().src='http://www.mouto.org/down.php?did='+encodeURIComponent(A.src);*/
                    A.play();
                    run = 1;
                    if (_img.complete) {
                        setTimeout(_img.onload, 10)
                    }
                    if (win.dm) {
                        dm.load(M.xid)
                    }
                    if (win.lrc) {
                        lrc.load(M.xid)
                    }
                    /*判断当前是否喜欢这首曲子*/
                    if (win.U) {
                        U.iflike(M.xid, function (r) {
                            $('#like').className = r ? 'a' : ''
                        })
                    }
                    /*fix 判断即将播放的曲子是否在待播放列表首位 */
                    if (playList[0] == M.xid) {
                        playList.shift()
                    }
                    /*如果待播放列表还有歌曲 就显示下一首 不然 隐藏*/
                    setTimeout(function () {
                        if (playList.length) {
                            $('#ctrl').className = '';
                            $('#next').href = '#!' + playList[0]
                        } else {
                            $('#ctrl').className = 'h load'
                        }
                    }, 3e3);
                    console.log(fm.A);
                    /*如果待播放列表剩余不及3首 那么载入更多*/
                    if (playList.length < 3) {
                        add()
                    }
                },
                song: function (id) {
                    var f = function (r) {
                        fm.play(r)
                    };
                    if (list[id]) {
                        f(list[id])
                    } else {
                        x('nmfm.php?a=song&id=' + id + '&_r=' + Math.random(), function (r) {
                            if (r.error) {
                                alert(r.error);
                                return add(fm.rid)
                            }
                            for (var l = r.length, i = 0; i < l; i++) {
                                list[r[i].xid] = r[i];
                                if (i != 0)
                                    playList.push(r[i].xid)
                            }
                            f(r[0])
                        })
                    }
                },
                log: function (pid) {
                    x('nmfm.php?a=log', 'pid=' + pid, function (r) {
                        console.log(r)
                    })
                },
                next: function () {
                    fm.log(location.hash.match(/\d+/));
                    setTimeout(function () {
                        location.href = $('#next').href
                    }, 3e3)
                },
                time: function () {
                    return A.currentTime * 1000;
                },
                adjust: function (i) {
                    i = i || 1;
                    A.currentTime += i * 5
                }
            };
        fm.A.onerror = function (e) {
            console.log(e);
            x('nmfm.php?a=report', 'type=' + e.type + '&timeStamp=' + e.timeStamp, function (g) {
                console.log(g)
            });
            fm.next()
        };
        $.j('i/plan.js?v=' + Math.random());
        $('#play').onclick = function () {
            if (A.paused) {
                A.play();
                $('#play').className = 'p';
                $('#play2').className = 'h'
            } else {
                A.pause();
                $('#play').className = 'play';
                $('#play2').className = ''
            }
        };
        $('#like').onclick = function () {
            if (!U.me) {
                return alert('尚未完成登录')
            }
            if ($('#like').className == 'a') {
                return
            }
            if (win.dm) {
                dm.send('like')
            }
            $('#like').className = 'a';
            x('x/?a=like', 'xid=' + location.hash.match(/\/[\d]+/), function (r) {
                /*console.log(r);*/
                if (r.error) {
                    return alert(r.error)
                }
                /*$('#like').className='a'*/
            });
        };
        $('img').onload = function () {
            this.className = ''
        };
        $('.info p').onclick = function () {
            $('input[name="k"]').value = this.innerHTML;
            $('#showS').click()
        };
        $('#play2').onclick = $('#play').onclick;
        var UA = navigator.userAgent;
        if (UA.match(/ip(ad|hone)/i)) {
            $('#play2').className = ''
        }
        $('meta[name="viewport"]').content = UA.match(/ipad/i) ? 'width=1024,user-scalable=no,minimal-ui' : UA.match(/iphone/i) ? 'width=520,user-scalable=no,minimal-ui' : 'width=720';
        var laHash = '简直惨惨惨OAQ',
            popstate = function () {
                var lash = location.hash.substring(2);
                if ('onhashchange' in win) {
                    win.onhashchange = popstate
                }
                if (laHash == location.hash) {
                    return
                }
                if (lash.match(/\d+/)) {
                    fm.song(lash.match(/\d+/))
                } else if (!run) {
                    add()
                }
                laHash = location.hash
            };
        setTimeout(popstate, 100);
        if (!'onhashchange' in win) {
            setInterval(function () {
                if (laHash != location.hash) {
                    popstate();
                    laHash = location.hash
                }
            }, 100)
        }
        console.log('「偷揉FM v7」<http://github.com/itorr/itorr.fm> @卜卜口 于 2015/5/23');
        return fm
    }(window, document);
var evalHtml = function (i) {
    x('i/' + i + '.html?v=' + Math.random(), function (H) {
        var div = $.D.m('div');
        div.innerHTML = H;
        $.D.a(div);
        eval(H.split('<script>')[1].split('<\/script>')[0])
    })
};
$.j('i/dm.js?v=' + Math.random());
$.j('i/lrc.js?v=' + Math.random());
$.j('i/u.js?v=' + Math.random());
evalHtml('search');
setTimeout(function () {
    $.lcss('i/star.css?v=' + Math.random());
    evalHtml('fo');
    evalHtml('key');
    evalHtml('menu');
    evalHtml('volume');
    evalHtml('rid');
    $.j('i/hotkey.js?v=' + Math.random());
    $.j('i/crop.js?v=' + Math.random());
    $.j('//1.mouto.org/x.js');
    $.j('i/fastclick.m.js?v=' + Math.random(), function () {
        FastClick.attach(document.body);
    })
}, 3e3);