var lrc = function (obj) {
    var lrc_arr = [],
        interval,
        lrc_li,
        noLrcLtl,
        lrc_loading = 0,
        offset = 0,
        lrc = {
            num: 0,
            load: function (id) {
                obj.className = 'h';
                clearTimeout(noLrcLtl);
                lrc_loading = 0;
                $.x('nmfm.php?a=lrc&id=' + id, function (txt) {
                    var txt_arr = txt.split('\n');
                    obj.innerHTML = '';
                    lrc.num = 0;
                    lrc_arr = [];
                    var _t_text,
                        _time,
                        _txt,
                        h = '',
                        _offset;
                    for (var i in txt_arr) {
                        _time = txt_arr[i].match(/\[\d{2}:\d{2}((\.|\:)(\d{3}))\]/g);
                        _txt = txt_arr[i].replace(/\[([0-9:.]{5,8}|[0-9:.]{5,10})\]/g, '');
                        _offset = txt_arr[i].match(/\[offset\:(\d+)\]/);
                        if (_offset) {
                            offset = _offset[1]
                        }
                        for (var _i in _time) {
                            _t_text = String(_time[_i]);
                            var _t_time = (_t_text.match(/\[[0-9]{2}/) + '').substr(1) * 60 + (_t_text.match(/\:[0-9]{2}/) + '').substr(1) * 1 + (_t_text.match(/[0-9]{3}\]/) + '').substr(0, 2) * 0.01666;
                            lrc_arr.push([_t_time, _txt])
                        }
                    }
                    lrc_arr.sort(function (a, b) {
                        return a[0] < b[0] ? -1 : 1
                    });
                    for (var i in lrc_arr) {
                        h += '<li>' + lrc_arr[i][1] + '</li>'
                    }
                    noLrcLtl = setTimeout(function () {
                        if (!txt) {
                            $('body').className = ''
                        } else {
                            $('body').className = 'showLrc';
                            lrc_loading = 1
                        }
                    }, 2000);
                    obj.innerHTML = h;
                    lrc_li = $.S('#lrc li');
                    setTimeout(function () {
                        obj.style.cssText = '';
                        obj.className = ''
                    }, 3000)
                })
            },
            step: function () {
                if (!lrc_loading) {
                    return
                }
                var Song_time = fm.time() / 1000 + .5 + (offset / 1000);
                for (var _i = 0; _i < lrc_arr.length; _i++) {
                    if (lrc_arr[_i][0] > Song_time) {
                        _i--;
                        break
                    }
                }
                if (_i < 0) {
                    _i = 0
                }
                var top = (-_i) * 50 + document.body.offsetHeight / 2 - 30;
                $.css($('#lrc'), 'transform:translateY(' + top + 'px);-moz-transform:translateY(' + top + 'px);-webkit-transform:translateY(' + top + 'px);');
                if ($('#lrc li.a')) {
                    $('#lrc li.a').className = ''
                }
                if (lrc_li[_i]) {
                    lrc_li[_i].className = 'a';
                    $('body').className = 'showLrc'
                } else if (lrc_arr.length == _i) {
                    $('body').className = ''
                }
            }
        };
    return lrc
}($('#lrc'));